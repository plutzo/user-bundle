<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Command;

use Marlinc\UserBundle\Entity\UserManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @internal
 */
final class ActivateUserCommand extends AbstractUserCommand
{
    protected static $defaultName = 'marlinc:user:activate';
    protected static $defaultDescription = 'Activate a user';

    protected function configure(): void
    {
        parent::configure();
        $this
            ->setHelp(
                <<<'EOT'
The <info>%command.full_name%</info> command activates a user (so they will be able to log in):

  <info>php %command.full_name% matthieu</info>
EOT
            );
    }

    protected function doExecute(UserInterface $user,InputInterface $input, OutputInterface $output): string
    {
        $user->setEnabled(true);

        $this->userManager->save($user);

        return sprintf('User "%s" has been activated.', $user->getEmail());
    }

}
