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
class UpdateStatusCommand extends Command
{
    protected function configure()
    {
        $this->setName('contacts:update:status')
             ->setDescription('Updates the status of Contact records.')
             ->setDefinition(array(
                 new InputOption('to_status', '-to', InputOption::VALUE_REQUIRED, 'Status to update to'),
                 new InputOption('from_status', '-from', InputOption::VALUE_REQUIRED, 'Status to filter by'),
                 new InputOption('created_before', null, InputOption::VALUE_REQUIRED, 'Created date to filter by'),
                 new InputOption('created_after', null, InputOption::VALUE_REQUIRED, 'Created date to filter by'),
                 new InputOption('list', '-l', InputOption::VALUE_REQUIRED, 'List Name or ID to filter by'),
                 new InputOption('token', '-t', InputOption::VALUE_REQUIRED, 'Bronto Token ID')
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

        /* @var $contactObject \Bronto_Api_Contact */
        $contactObject = $bronto->getContactObject();

        /* @var $listObject \Bronto_Api_List */
        $listObject = $bronto->getListObject();

        //
        // From Status
        if (!$fromStatus = $input->getOption('from_status')) {
            $statusList = $contactObject->getOptionValues('status');
            if ($dialog->askConfirmation($output, '<question>Do you want to filter Contacts by their current status? (y/n)</question> ', false)) {
                $output->writeln('');
                $output->writeln(sprintf('<info>Available status values: %s</info>', implode(', ', $statusList)));
                $fromStatus = $dialog->ask($output, 'Enter status to filter by: ', null);
                $output->writeln('');
            }
        }

        if (!empty($fromStatus)) {
            $fromStatus = trim(strtolower($fromStatus));
            if (!$contactObject->isValidOptionValue('status', $fromStatus)) {
                throw new \InvalidArgumentException(sprintf("Invalid filter status: '%s'", $fromStatus));
            }
            foreach ($statusList as $i => $status) {
                if ($status == $fromStatus) {
                    unset($statusList[$i]);
                    break;
                }
            }
        }

        //
        // List
        if (!$list = $input->getOption('list')) {
            if ($dialog->askConfirmation($output, '<question>Do you want to filter Contacts by a specific list? (y/n)</question> ', false)) {
                $output->writeln('');
                $list = $dialog->ask($output, 'Enter list name or ID to filter by: ', null);
                $output->writeln('');
            }
        }

        if (!empty($list)) {
            $testList = $listObject->createRow();
            if (strpos($list, ' ') === false && strlen($list) > 30) {
                // Possibly an ID
                $testList->id = $list;
            } else {
                // Try name
                $testList->name = $list;
            }
            $list = $testList->read();
            unset($testList);
        }

        //
        // Created After
        if (!$createdAfter = $input->getOption('created_after')) {
            if ($dialog->askConfirmation($output, '<question>Do you want to filter Contacts created AFTER a specific date? (y/n)</question> ', false)) {
                $output->writeln('');
                $createdAfter = $dialog->ask($output, 'Enter created AFTER date filter by (e.g. ' . date('Y-m-d', time() - (86400 * 7)) . '): ', null);
                $output->writeln('');
            }
        }

        if (!empty($createdAfter)) {
            $createdAfterTs = strtotime($createdAfter);
            $createdAfter   = date('c', $createdAfterTs);

            if ($createdAfterTs > time()) {
                throw new \InvalidArgumentException("Cannot filter Contacts by a created after date in the future (" . date('M j, Y', $createdAfterTs) . ')');
            }
        }

        //
        // Created Before
        if (!$createdBefore = $input->getOption('created_before')) {
            if ($dialog->askConfirmation($output, '<question>Do you want to filter Contacts created BEFORE a specific date? (y/n)</question> ', false)) {
                $output->writeln('');
                $createdBefore = $dialog->ask($output, 'Enter created BEFORE date filter by (e.g. ' . date('Y-m-d') . '): ', null);
            }
        }

        if (!empty($createdBefore)) {
            $createdBeforeTs = strtotime($createdBefore);
            $createdBefore   = date('c', $createdBeforeTs);

            if (!empty($createdAfter)) {
                if ($createdBeforeTs > $createdAfterTs) {
                    throw new \InvalidArgumentException("Cannot filter Contacts by a created before date that comes AFTER the created after date filter.");
                }
            }
        }

        //
        // To Status
        if (!$toStatus = $input->getOption('to_status')) {
            $output->writeln('');
            do {
                $output->writeln(sprintf('<info>Available status values: %s</info>', implode(', ', $statusList)));
                $toStatus = $dialog->ask($output, 'Enter status to update to: ', null);
                $toStatus = trim(strtolower($toStatus));
                $retry = false;
                if (!$contactObject->isValidOptionValue('status', $toStatus)) {
                    $retry = true;
                    $output->writeln('');
                    $output->writeln(sprintf("<error>Invalid update status: '%s'</error>", $toStatus));
                    $output->writeln('');
                }
            } while ($retry);
        }

        $contactsFilter = array();

        //
        // Confirmation message
        $message = 'Preparing to update ';
        if (empty($fromStatus)) {
            $message .= '*all* Contacts ';
        } else {
            $contactsFilter['status'] = array($fromStatus);
            $message .= "all '{$fromStatus}' Contacts ";
        }
        if (!empty($createdAfter) && $createdAfterTs) {
            $contactsFilter['created'] = array(
                'operator' => 'After',
                'value'    => $createdAfter,
            );
            $message .= 'created after ' . date('M j, Y', $createdAfterTs) . ' ';
        }
        if (!empty($createdBefore) && $createdBeforeTs) {
            if (!empty($createdAfter) && $createdAfterTs) {
                $contactsFilter['created'] = array(
                    array(
                        'operator' => 'Before',
                        'value'    => $createdBefore,
                    ),
                    $contactsFilter['created']
                );
                $message .= 'and ';
            } else {
                $contactsFilter['created'] = array(
                    'operator' => 'Before',
                    'value'    => $createdBefore,
                );
                $message .= 'created ';
            }
            $message .= 'before ' . date('M j, Y', $createdBeforeTs) . ' ';
        }
        if (!empty($list)) {
            $contactsFilter['listId'] = array($list->id);
            $message .= "and on list '" . $list->name . "' ";
        }
        $message .= "to '{$toStatus}'.";

        $output->writeln('');
        $output->writeln('');
        $output->writeln("<comment>NOTICE:</comment>");
        $output->writeln("<comment>{$message}</comment>");
        $output->writeln('');

        //
        // Confirm
        if (!$dialog->askConfirmation($output, sprintf("<question>Are you sure (y/n)? </question> "), false)) {
            return;
        }

        // Get contacts
        $contactsCounter = 0;
        $contactsPage    = 1;
        while ($contacts = $contactObject->readAll($contactsFilter, array(), false, $contactsPage)) {
            if (!$contacts->count()) {
                break;
            }

            $output->writeln('');
            $output->writeln(sprintf('Processing page %d - %d Contact(s)...', $contactsPage, $contacts->count()));
            $output->writeln('');
            $progress->start($output, $contacts->count(), array('format' => ProgressHelper::FORMAT_VERBOSE));

            $internalCounter = 0;
            foreach ($contacts as $contact /* @var $contact Bronto_Api_Contact_Row */) {
                $contact->status = $toStatus;
                $contact->persist();

                $progress->advance();
                $contactsCounter++;
                $internalCounter++;

                if ($internalCounter % 50 == 0 || $internalCounter >= ($contacts->count() - 1)) {
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

            $progress->finish();
            $contactsPage++;
        }

        if ($contactsCounter == 0) {
            $output->writeln('');
        }
        $output->writeln('Complete!');
        $output->writeln(sprintf('Total Contacts successfully updated: %d', $contactsCounter));
    }
}