<?php


namespace Marlinc\UserBundle\Command;

use Marlinc\UserBundle\Entity\UserManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractUserCommand extends Command
{
    protected UserManagerInterface $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        parent::__construct();

        $this->userManager = $userManager;
    }

    /**
     * @param object $user
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    abstract protected function doExecute(object $user,InputInterface $input,OutputInterface $output): string;

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