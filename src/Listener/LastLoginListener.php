<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Listener;

use Marlinc\UserBundle\Entity\UserInterface;
use Marlinc\UserBundle\Entity\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * @internal
 */
final class LastLoginListener implements EventSubscriberInterface
{
    private UserManagerInterface $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        ];
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $user->setLastLogin(new \DateTime());
        $this->userManager->save($user);
    }
}
