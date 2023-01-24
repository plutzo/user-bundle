<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Entity;

use Sonata\Doctrine\Model\ManagerInterface;

/**
 * @phpstan-extends ManagerInterface<\Marlinc\UserBundle\Entity\UserInterface>
 */
interface UserManagerInterface extends ManagerInterface
{
    public function updatePassword(UserInterface $user): void;

    public function findUserByEmail(string $email): ?UserInterface;
}
