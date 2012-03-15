<?php

namespace Console\Command\Lists;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Console\Application getApplication() getApplication()
 */
class GetAllCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('lists:get:all')
            ->setDescription('Returns all Lists')
            ->setDefinition(array(
                 new InputOption('token', '-t', InputOption::VALUE_OPTIONAL, 'Bronto Token ID (optional)')
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

        /* @var $listObject \Bronto_Api_List */
        $listObject = $bronto->getListObject();

        // Get Lists
        $listCounter = 0;
        $listPage    = 1;
        while ($lists = $listObject->readAll(array(), array(), false, $listPage)) {
            if (!$lists->count()) {
                break;
            }

            $output->writeln(sprintf('Processing page %d - %d List(s)...', $listPage, $lists->count()));

            $internalCounter = 0;
            foreach ($lists as $list /* @var $contact Bronto_Api_List_Row */) {
                $output->writeln(' - ' . $list->name);
                $listCounter++;
                $internalCounter++;
            }

            $listPage++;
            break;
        }

        $output->writeln('');
        $output->writeln('Complete!');
    }
}