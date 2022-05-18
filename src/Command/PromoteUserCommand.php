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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class PromoteUserCommand extends Command
{
    protected static $defaultName = 'marinc:user:promote';
    protected static $defaultDescription = 'Promotes a user by adding a role';

    private UserManagerInterface $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        parent::__construct();

        $this->userManager = $userManager;
    }

    protected function configure(): void
    {
        \assert(null !== static::$defaultDescription);

        $this
            ->setDescription(static::$defaultDescription)
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('role', InputArgument::OPTIONAL, 'The role'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Instead specifying role, use this to quickly add the super administrator role'),
            ])
            ->setHelp(
                <<<'EOT'
The <info>%command.full_name%</info> command promotes a user by adding a role

  <info>php %command.full_name% matthieu ROLE_CUSTOM</info>
  <info>php %command.full_name% --super-admin matthieu</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');
        $superAdmin = (true === $input->getOption('super-admin'));

        if (null !== $role && $superAdmin) {
            throw new \InvalidArgumentException('You can pass either the role or the --super-admin option (but not both simultaneously).');
        }

        if (null === $role && !$superAdmin) {
            throw new \InvalidArgumentException('Not enough arguments.');
        }

        $user = $this->userManager->findUserByEmail($email);

        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" email does not exist.', $email));
        }

        if ($superAdmin) {
            $user->setSuperAdmin(true);

            $output->writeln(sprintf('User "%s" has been promoted as a super administrator. This change will not apply until the user logs out and back in again.', $email));
        } elseif (!$user->hasRole($role)) {
            $user->addRole($role);

            $output->writeln(sprintf('Role "%s" has been added to user "%s". This change will not apply until the user logs out and back in again.', $role, $email));
        } else {
            $output->writeln(sprintf('User "%s" did already have "%s" role.', $email, $role));
        }

        $this->userManager->save($user);

        return 0;
    }
}
