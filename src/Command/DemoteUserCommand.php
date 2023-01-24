<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Command;

use Marlinc\UserBundle\Entity\UserInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class DemoteUserCommand extends AbstractUserCommand
{
    protected static $defaultName = 'marlinc:user:demote';
    protected static $defaultDescription = 'Demotes a user by removing a role';

    protected function configure(): void
    {
        parent::configure();
        $this
            ->addArgument('role', InputArgument::OPTIONAL, 'The role')
            ->addOption('super-admin', null, InputOption::VALUE_NONE,
            'Instead specifying role, use this to quickly add the super administrator role')
            ->setHelp(
            <<<'EOT'
The <info>%command.full_name%</info> command demotes a user by removing a role

  <info>php %command.full_name% matthieu ROLE_CUSTOM</info>
  <info>php %command.full_name% --super-admin matthieu</info>
EOT
        );
    }

    protected function doExecute(UserInterface $user,InputInterface $input, OutputInterface $output): string
    {
        $role = $input->getArgument('role');
        $superAdmin = (true === $input->getOption('super-admin'));
        $message = '';

        if (null !== $role && $superAdmin) {
            throw new \InvalidArgumentException('You can pass either the role or the --super-admin option (but not both simultaneously).');
        }

        if (null === $role && !$superAdmin) {
            throw new \InvalidArgumentException('Not enough arguments.');
        }

        if ($superAdmin) {

            $user->setSuperAdmin(false);
            $message = sprintf('User "%s" has been demoted as a simple user. This change will not apply until the user logs out and back in again.', $user->getEmail());

        } elseif ($user->hasRole($role)) {

            $user->removeRole($role);
            $message = sprintf('Role "%s" has been removed from user "%s". This change will not apply until the user logs out and back in again.', $role, $user->getEmail());

        } else {

            $message = sprintf('User "%s" didn\'t have "%s" role.', $user->getEmail(), $role);

        }

        $this->userManager->save($user);

        return $message;
    }

}
