<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Marlinc\UserBundle\Command;

use Marlinc\UserBundle\Entity\UserManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @internal
 */
final class PromoteUserCommand extends AbstractUserCommand
{
    protected static $defaultName = 'marlinc:user:promote';
    protected static $defaultDescription = 'Promotes a user by adding a role';

    protected function configure(): void
    {
        parent::configure();
        $definition = $this->getDefinition();
        $definition->addArgument(new InputArgument('role', InputArgument::OPTIONAL, 'The role'));
        $definition->addOption(new InputOption('super-admin', null, InputOption::VALUE_NONE,
            'Instead specifying role, use this to quickly add the super administrator role'));

        $this->setHelp(
            <<<'EOT'
The <info>%command.full_name%</info> command promotes a user by adding a role

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
            $user->setSuperAdmin(true);

            $message = sprintf('User "%s" has been promoted as a super administrator. This change will not apply until the user logs out and back in again.', $user->getEmail());
        } elseif (!$this->user->hasRole($role)) {
            $user->addRole($role);

            $message = sprintf('Role "%s" has been added to user "%s". This change will not apply until the user logs out and back in again.', $role, $user->getEmail());
        } else {
            $message = sprintf('User "%s" did already have "%s" role.', $user->getEmail(), $role);
        }

        $this->userManager->save($user);

        return $message;
    }

}
