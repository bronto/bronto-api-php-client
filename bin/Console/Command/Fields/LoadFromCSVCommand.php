<?php

namespace Console\Command\Fields;

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
            ->setName('fields:load:csv')
            ->setDescription('Creates fields based on the header of a CSV file')
            ->setDefinition(array(
                new InputOption('token', '-t', InputOption::VALUE_REQUIRED, 'Bronto Token ID'),
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

        $headers  = array();
        $fields   = array();
        $filepath = $input->getArgument('file');
        $filesize = filesize($filepath);

        if (!($fh = @fopen($filepath, 'r'))) {
            throw new \RuntimeException('Unable to open file for reading: ' . $filepath);
        }

        $output->writeln('');
        $progress->start($output, $filesize, array('redrawFreq' => 1024, 'format' => ProgressHelper::FORMAT_VERBOSE));

        $count = 0;
        while (($row = fgetcsv($fh)) !== false) {
            $count++;
            if ($count === 1) {
                // Store header values
                foreach ($row as $i => $columnValue) {
                    $headers[$i] = $columnValue;
                    $fields[$columnValue] = array();
                }
            } else {
                // Store each data value
                foreach ($row as $i => $columnValue) {
                    // Don't store empties
                    $headerName = $headers[$i];
                    if ($columnValue !== '' || count($fields[$headerName]) <= 100) {
                        $fields[$headerName][$columnValue] = true;
                    }
                }
            }
            $bytes = implode('","', $row);
            $progress->advance(strlen($bytes));
        }

        $progress->finish();
        $output->writeln('');
        $output->writeln('');

        // Cleanup...
        unset($count, $bytes, $headers, $filesize);

        /* @var $fieldObject Bronto_Api_Field */
        $fieldObject = $bronto->getFieldObject();

        $fails = 0;
        $count = 0;
        foreach ($fields as $name => $values) {
            $count++;
            $normalizedName = $fieldObject->normalize($name);
            $guessedType    = $fieldObject->guessType($normalizedName, $values);

            $field = $fieldObject->createRow();
            if (is_array($guessedType)) {
                foreach ($guessedType as $predefinedName => $predefinedOptions) {
                    $field->name  = $predefinedName;
                    $field->label = $predefinedOptions['label'];
                    $field->type  = $predefinedOptions['type'];
                    if (isset($predefinedOptions['options'])) {
                        $field->options = $predefinedOptions['options'];
                    }
                }
            } else {
                $field->name  = $normalizedName;
                $field->label = $name;
                $field->type  = $guessedType;
            }

            $output->writeln('');
            $output->write(sprintf("Creating <comment>%s</comment> as new field <info>%s</info> <error>(%s)</error>\n", $name, $field->name, $field->type));
            if (!$dialog->askConfirmation($output, '<question>Continue (Y)/n?</question> ', true)) {
                $output->writeln("    <error>Skipping {$name}...</error>");
                continue;
            }

            try {
                $field->save();
            } catch (Exception $e) {
                $fails++;
                $output->writeln('<error>' . $e->getMessage() . '</error>');
            }
        }

        $output->writeln("\nSuccess: {$count}");
        $output->writeln("\nFailures: {$fails}");
        $output->writeln("<info>Done</info> :)");
    }
}