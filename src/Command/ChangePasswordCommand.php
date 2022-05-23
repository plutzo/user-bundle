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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class ChangePasswordCommand extends AbstractUserCommand
{
    protected static $defaultName = 'marlinc:user:change-password';
    protected static $defaultDescription = 'Change the password of a user';

    protected function configure(): void
    {
        \assert(null !== static::$defaultDescription);

        $this
            ->setDescription(static::$defaultDescription)
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
            ])
            ->setHelp(
                <<<'EOT'
The <info>%command.full_name%</info> command changes the password of a user:

  <info>php %command.full_name% matthieu mypassword</info>

EOT
            );
    }

    protected function doExecute(object $user,InputInterface $input, OutputInterface $output): string
    {
        $password = $input->getArgument('password');
        $user->setPlainPassword($password);
        $this->userManager->updatePassword($user);
        $this->userManager->save($user);

        return sprintf('Changed password for user "%s".', $user->getEmail());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input , $output);
        return 0;
    }

}
