<?php

namespace Marlinc\UserBundle\Command;

use Marlinc\UserBundle\Util\UserManipulator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUserCommand extends Command
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
            ->setName('marlinc:user:create')
            ->setDescription('Create a user.')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputArgument('firstname', InputArgument::REQUIRED, 'The first name'),
                new InputArgument('lastname', InputArgument::REQUIRED, 'The last name'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
            ])
            ->setHelp(<<<'EOT'
The <info>marlinc:user:create</info> command creates a user:

  <info>php %command.full_name% it@aha.biz</info>

This interactive shell will ask you for a password and the user's first and last name.

You can alternatively specify these options as arguments:

  <info>php %command.full_name% it@aha.biz mypassword Paul Panzer</info>

You can create a super admin via the super-admin flag:

  <info>php %command.full_name% it@aha.biz --super-admin</info>

You can create an inactive user (will not be able to log in):

  <info>php %command.full_name% it@aha.biz --inactive</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $firstname = $input->getArgument('firstname');
        $lastname = $input->getArgument('lastname');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $inactive = $input->getOption('inactive');
        $superadmin = $input->getOption('super-admin');

        $this->userManipulator->create($email, $password, $firstname, $lastname, !$inactive, $superadmin);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $email));
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = [];

        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \Exception('Email can not be empty');
                }

                return $email;
            });
            $questions['email'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('Password can not be empty');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        if (!$input->getArgument('firstname')) {
            $question = new Question('Please choose a first name:');
            $question->setValidator(function ($username) {
                if (empty($username)) {
                    throw new \Exception('Frist name can not be empty');
                }

                return $username;
            });
            $questions['firstname'] = $question;
        }

        if (!$input->getArgument('lastname')) {
            $question = new Question('Please choose a last name:');
            $question->setValidator(function ($username) {
                if (empty($username)) {
                    throw new \Exception('Last name can not be empty');
                }

                return $username;
            });
            $questions['lastname'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
