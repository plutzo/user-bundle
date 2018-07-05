<?php

namespace Marlinc\UserBundle\EventListener;

use Marlinc\UserBundle\Event\FilterUserResponseEvent;
use Marlinc\UserBundle\Event\UserEvent;
use Marlinc\UserBundle\Event\UserEvents;
use Marlinc\UserBundle\Model\UserInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallContext;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

/**
 * Automatically login the user after certain events:
 * - Successful registration
 * - Successful password reset
 *
 * @package Marlinc\UserBundle\EventListener
 */
class AuthenticationListener implements EventSubscriberInterface
{
    /**
     * @var UserCheckerInterface
     */
    private $userChecker;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var SessionAuthenticationStrategyInterface
     */
    private $sessionStrategy;

    /**
     * @var FirewallContext
     */
    private $firewallContext;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(UserCheckerInterface $userChecker,
                                RequestStack $requestStack,
                                SessionAuthenticationStrategyInterface $sessionStrategy,
                                FirewallContext $firewallContext,
                                TokenStorageInterface $tokenStorage)
    {
        $this->userChecker = $userChecker;
        $this->requestStack = $requestStack;
        $this->sessionStrategy = $sessionStrategy;
        $this->firewallContext = $firewallContext;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            UserEvents::REGISTRATION_COMPLETED => 'authenticate',
            UserEvents::REGISTRATION_CONFIRMED => 'authenticate',
            UserEvents::RESETTING_RESET_COMPLETED => 'authenticate',
        ];
    }

    /**
     * @param FilterUserResponseEvent $event
     * @param string $eventName
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function authenticate(FilterUserResponseEvent $event, $eventName, EventDispatcherInterface $eventDispatcher)
    {
        try {
            $user = $event->getUser();

            $this->userChecker->checkPreAuth($user);

            $token = $this->createAuthenticatedToken($user, $this->firewallContext->getConfig()->getName());
            $request = $this->requestStack->getCurrentRequest();

            $this->migrateSession($request, $token);
            $this->tokenStorage->setToken($token);

            $eventDispatcher->dispatch(UserEvents::SECURITY_IMPLICIT_LOGIN, new UserEvent($event->getUser(), $event->getRequest()));
        } catch (AccountStatusException $ex) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     */
    private function migrateSession(Request $request, TokenInterface $token)
    {
        if ($this->sessionStrategy && $request->hasSession() && $request->hasPreviousSession()) {
            $this->sessionStrategy->onAuthentication($request, $token);
        }
    }

    /**
     * @param UserInterface $user
     * @param string $providerKey
     * @return PostAuthenticationGuardToken
     */
    private function createAuthenticatedToken(UserInterface $user, $providerKey)
    {
        return new PostAuthenticationGuardToken(
            $user,
            $providerKey,
            $user->getRoles()
        );
    }
}
