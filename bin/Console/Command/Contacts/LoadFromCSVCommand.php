<?php

namespace Console\Command\Contacts;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface,
    Console\Helper\ProgressHelper;

/**
 * @method \Console\Application getApplication() getApplication()
 */
class LoadFromCSVCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('contacts:load:csv')
            ->setDescription('Creates fields based on the header of a CSV file')
            ->setDefinition(array(
                new InputOption('token', '-t', InputOption::VALUE_REQUIRED, 'Bronto Token ID'),
                new InputOption('restart', null, InputOption::VALUE_REQUIRED, 'Restart from a specific point in CSV'),
                new InputArgument('file', InputArgument::REQUIRED, 'The CSV file to read'),
            ));

        parent::configure();
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     * @return integer 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $dialog \Symfony\Component\Console\Helper\DialogHelper */
        $dialog = $this->getHelperSet()->get('dialog');

        /* @var $progress \Console\Helper\ProgressHelper */
        $progress = $this->getHelperSet()->get('progress');

        //
        // Token
        if (!$token = $input->getOption('token')) {
            $token = $dialog->ask($output, 'Enter Bronto API Token: ', null);
            if (empty($token)) {
                throw new \InvalidArgumentException('Bronto API Token is required');
            }
            $output->writeln('');
        }

        /* @var $bronto \Bronto_Api */
        $bronto = $this->getApplication()->getApi();
        $bronto->setToken($token);
        $bronto->login();

        $headers   = array();
        $csvFields = array();
        $csvRows   = 0;
        $filepath  = $input->getArgument('file');
        $filesize  = filesize($filepath);

        if (!($fh = @fopen($filepath, 'r'))) {
            throw new \RuntimeException('Unable to open file for reading: ' . $filepath);
        }

        $count = 0;
        while (($row = fgetcsv($fh)) !== false) {
            $count++;
            $csvRows++;
            if ($count === 1) {
                $output->writeln('');
                $output->writeln('Reading headings...');
                $progress->start($output, count($row), array('format' => ProgressHelper::FORMAT_VERBOSE));
                // Store header values
                foreach ($row as $i => $columnValue) {
                    $headers[$i] = $columnValue;
                    $csvFields[$columnValue] = array();
                    $progress->advance();
                }
                $progress->finish();
            } else {
                if ($count === 2) {
                    $output->writeln('');
                    $output->writeln('Preparing to guess types...');
                    $progress->start($output, $filesize, array('redrawFreq' => 1024, 'format' => ProgressHelper::FORMAT_VERBOSE));
                }
                // Store each data value
                foreach ($row as $i => $columnValue) {
                    // Don't store empties
                    $headerName = $headers[$i];
                    if ($columnValue !== '' || count($csvFields[$headerName]) <= 100) {
                        $csvFields[$headerName][$columnValue] = true;
                    }
                }
                $bytes = implode('","', $row);
                $progress->advance(strlen($bytes));
            }
        }

        fclose($fh);
        $progress->finish();

        $output->writeln('');
        $output->writeln('Checking fields...');

        /* @var $fieldObject \Bronto_Api_Field */
        $fieldObject = $bronto->getFieldObject();

        $progress->start($output, count($csvFields), array('format' => ProgressHelper::FORMAT_VERBOSE));
        $fields = array();
        $count  = 0;
        foreach ($csvFields as $name => $values) {
            $count++;
            $normalizedName = $fieldObject->normalize($name);
            $guessedType    = $fieldObject->guessType($normalizedName, $values);

            $field = $fieldObject->createRow();
            if (is_array($guessedType)) {
                foreach ($guessedType as $predefinedName => $predefinedOptions) {
                    $field->name = $predefinedName;
                }
            } else {
                $field->name = $normalizedName;
            }

            try {
                $field->read();
                if ($field->id) {
                    $fields[$name] = $field;
                }
            } catch (\Exception $e) {
                $output->writeln(' <error>' . $e->getMessage() . '</error>');
            }

            $progress->advance();
        }

        unset($csvFields);
        $progress->finish();

        if ($startAt = $input->getOption('restart')) {
            $output->writeln('');
            $output->writeln("Loading contacts (starting from {$startAt})...");
        } else {
            $startAt = 1;
            $output->writeln('');
            $output->writeln('Loading contacts...');
        }

        /* @var $contactObject \Bronto_Api_Contact */
        $contactObject = $bronto->getContactObject();

        if (!($fh = @fopen($filepath, 'r'))) {
            throw new \RuntimeException('Unable to open file for reading: ' . $filepath);
        }

        $progress->start($output, $csvRows, array('format' => ProgressHelper::FORMAT_VERBOSE));

        if ($startAt > 0) {
            $progress->advance($startAt);
        }

        $count = 0;
        while (($row = fgetcsv($fh)) !== false) {
            $count++;
            if ($count > $startAt) {
                /* @var $contact \Bronto_Api_Contact_Row */
                $contact = $contactObject->createRow();
                foreach ($row as $i => $columnValue) {
                    if (!empty($columnValue)) {
                        $headerName = $headers[$i];
                        if (isset($fields[$headerName])) {
                            $contact->setField($fields[$headerName], $columnValue);
                        } else {
                            if (strtolower($headerName) == 'email') {
                                $contact->email = $columnValue;
                            }
                        }
                    }
                }
                $contact->persist();
                $progress->advance();

                if ($count % 50 == 0 || $count >= ($csvRows - 1)) {
                    try {
                        /* @var $rowset \Bronto_Api_Rowset */
                        $rowset = $contactObject->flush();
                        if ($rowset->hasErrors()) {
                            foreach ($rowset->getErrors() as $error) {
                                $output->writeln(' <error>' . $error['message'] . '</error>');
                            }
                        }
                    } catch (\Exception $e) {
                        $output->writeln(' <error>' . $e->getMessage() . '</error>');
                    }
                }
            }
        }

        fclose($fh);
        $progress->finish();

        @rename($filepath, "{$filepath}.loaded");

        $output->writeln('');
        $output->writeln("<info>Done</info> :)");
    }
}