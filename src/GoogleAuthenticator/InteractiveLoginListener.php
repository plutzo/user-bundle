<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\GoogleAuthenticator;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class InteractiveLoginListener
{
    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * @param Authenticator $authenticator
     */
    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        if (! $this->authenticator->needToHaveGoogle2FACode($event->getRequest())) {
            return;
        }

        if (! $event->getAuthenticationToken() instanceof UsernamePasswordToken) {
            return;
        }

        $token = $event->getAuthenticationToken();

        if (! $token->getUser()->getTwoStepVerificationCode()) {
            return;
        }

        $event->getRequest()->getSession()->set($this->authenticator->getSessionKey($token), null);
    }
}
