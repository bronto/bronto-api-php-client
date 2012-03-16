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
class DeleteAllCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('fields:delete:all')
            ->setDescription('Deletes all fields')
            ->setDefinition(array(
                new InputOption('token', '-t', InputOption::VALUE_REQUIRED, 'Bronto Token ID'),
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

        $iterator = $bronto->getFieldObject()->readAll()->iterate();
        foreach ($iterator as $field /* @var $field Bronto_Api_Field_Row */) {
            if ($iterator->isNewPage()) {
                $progress->finish();
                $output->writeln('');
                $progress->start($output, $iterator->count());
            }

            $field->delete();
            $progress->advance();
        }

        $progress->finish();
        
        $output->writeln('');
        $output->writeln('');
        $output->writeln('<info>Done!</info>');
    }
}