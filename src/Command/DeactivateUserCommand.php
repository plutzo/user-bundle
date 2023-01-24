<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Command;

use Marlinc\UserBundle\Entity\UserInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class DeactivateUserCommand extends AbstractUserCommand
{
    protected static $defaultName = 'marlinc:user:deactivate';
    protected static $defaultDescription = 'Deactivate a user';

    protected function configure(): void
    {
        parent::configure();
        $this
            ->setHelp(
                <<<'EOT'
The <info>%command.full_name%</info> command deactivates a user (so they will be unable to log in):

  <info>php %command.full_name% matthieu</info>
EOT
            );
    }

    protected function doExecute(UserInterface $user,InputInterface $input, OutputInterface $output): string
    {
        $user->setEnabled(false);
        $this->userManager->save($user);

        return sprintf('User "%s" has been activated.', $user->getEmail());
    }

}
