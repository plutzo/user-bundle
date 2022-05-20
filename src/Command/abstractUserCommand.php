<?php


namespace Marlinc\UserBundle\Command;

use Marlinc\UserBundle\Entity\BaseUser;
use Marlinc\UserBundle\Entity\UserManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class abstractUserCommand extends Command
{
    protected UserManagerInterface $userManager;
    protected  $user;

    public function __construct(UserManagerInterface $userManager)
    {
        parent::__construct();

        $this->userManager = $userManager;
    }

    abstract protected function doExecute($user, $input, $output): string;

    protected function Execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');

        $this->user = $this->userManager->findUserByEmail($email);

        if (null === $this->user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" email does not exist.', $email));
        }

        return Command::SUCCESS;
    }

}