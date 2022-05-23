<?php


namespace Marlinc\UserBundle\Command;

use Marlinc\UserBundle\Entity\UserManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractUserCommand extends Command
{
    protected UserManagerInterface $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        parent::__construct();

        $this->userManager = $userManager;
    }

    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
            ]);
    }

    /**
     * Implement this method to run the code for the command.
     * The return value will be displayed in the command line.
     */
    abstract protected function doExecute(UserInterface $user, InputInterface $input, OutputInterface $output): string;

    protected function Execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');

        $user = $this->userManager->findUserByEmail($email);

        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" email does not exist.', $email));
        }

        $output->writeln($this->doExecute($user, $input,$output));
        return Command::SUCCESS;
    }

}