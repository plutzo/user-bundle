<?php

namespace Marlinc\UserBundle\Command;

use Marlinc\UserBundle\Util\UserManipulator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class ActivateUserCommand extends Command
{
    /**
     * @var UserManipulator
     */
    private $userManipulator;

    public function __construct(UserManipulator $userManipulator)
    {
        parent::__construct();

        $this->userManipulator = $userManipulator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('marlinc:user:activate')
            ->setDescription('Activate a user')
            ->setDefinition([
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
            ])
            ->setHelp(<<<'EOT'
The <info>fos:user:activate</info> command activates a user (so they will be able to log in):

  <info>php %command.full_name% matthieu</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');

        $this->userManipulator->activate($username);

        $output->writeln(sprintf('User "%s" has been activated.', $username));
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $question = new Question('Please choose a username:');
            $question->setValidator(function ($username) {
                if (empty($username)) {
                    throw new \Exception('Username can not be empty');
                }

                return $username;
            });
            $answer = $this->getHelper('question')->ask($input, $output, $question);

            $input->setArgument('username', $answer);
        }
    }
}
