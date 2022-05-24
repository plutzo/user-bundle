<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata as ORMClassMetadata;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Marlinc\UserBundle\Entity\UserInterface;
use Marlinc\UserBundle\Entity\UserManagerInterface;

/**
 * @internal
 */
final class UserListener implements EventSubscriber
{
    private UserManagerInterface $userManager;

    public function __construct(UserManagerInterface $userManager) {
        $this->userManager = $userManager;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof UserInterface) {
            return;
        }

        $this->updateUser($object);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof UserInterface) {
            return;
        }

        $this->updateUser($object);
        $this->recomputeChangeSet($args->getObjectManager(), $object);
    }

    private function updateUser(UserInterface $user): void
    {
        $this->userManager->updatePassword($user);
    }

    private function recomputeChangeSet(ObjectManager $om, UserInterface $user): void
    {
        $meta = $om->getClassMetadata(\get_class($user));

        if ($om instanceof EntityManagerInterface) {
            \assert($meta instanceof ORMClassMetadata);

            $om->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);
        }
    }
}
