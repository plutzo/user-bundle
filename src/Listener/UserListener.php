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

namespace Marlinc\UserBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata as ORMClassMetadata;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Marlinc\UserBundle\Entity\UserInterface;
use Marlinc\UserBundle\Entity\UserManagerInterface;
use Marlinc\UserBundle\Util\CanonicalFieldsUpdaterInterface;

/**
 * @internal
 */
final class UserListener implements EventSubscriber
{
    private CanonicalFieldsUpdaterInterface $canonicalFieldsUpdater;

    private UserManagerInterface $userManager;

    public function __construct(
        CanonicalFieldsUpdaterInterface $canonicalFieldsUpdater,
        UserManagerInterface $userManager
    ) {
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
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
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
        $this->userManager->updatePassword($user);
    }

    private function recomputeChangeSet(ObjectManager $om, UserInterface $user): void
    {
        $meta = $om->getClassMetadata(\get_class($user));

        if ($om instanceof EntityManager) {
            \assert($meta instanceof ORMClassMetadata);

            $om->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);
        } 
    }
}
