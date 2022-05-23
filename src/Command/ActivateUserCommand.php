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
final class ActivateUserCommand extends AbstractUserCommand
{
    protected static $defaultName = 'marlinc:user:activate';
    protected static $defaultDescription = 'Activate a user';

    protected function configure(): void
    {
        \assert(null !== static::$defaultDescription);

        $this
            ->setDescription(static::$defaultDescription)
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
            ])
            ->setHelp(
                <<<'EOT'
The <info>%command.full_name%</info> command activates a user (so they will be able to log in):

  <info>php %command.full_name% matthieu</info>
EOT
            );
    }

    protected function doExecute(object $user,InputInterface $input, OutputInterface $output): string
    {
        $user->setEnabled(true);

        $this->userManager->save($user);

        return sprintf('User "%s" has been activated.', $user->getEmail());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input , $output );

        return 0;
    }
}
