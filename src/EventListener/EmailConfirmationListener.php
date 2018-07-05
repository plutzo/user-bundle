<?php

namespace Marlinc\UserBundle\EventListener;

use Marlinc\UserBundle\Event\FormEvent;
use Marlinc\UserBundle\Event\UserEvents;
use Marlinc\UserBundle\Mailer\MailerInterface;
use Marlinc\UserBundle\Model\UserInterface;
use Marlinc\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Send confirmation mail for registrations.
 * This function needs to be enabled by registering an event listener extending this class and
 * implementing a corresponding mailer service.
 *
 * @package Marlinc\UserBundle\EventListener
 */
abstract class EmailConfirmationListener implements EventSubscriberInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * EmailConfirmationListener constructor.
     *
     * @param MailerInterface         $mailer
     * @param TokenGeneratorInterface $tokenGenerator
     * @param UrlGeneratorInterface   $router
     * @param SessionInterface        $session
     */
    public function __construct(MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, UrlGeneratorInterface $router, SessionInterface $session)
    {
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            UserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onRegistrationSuccess(FormEvent $event)
    {
        /** @var UserInterface $user */
        $user = $event->getForm()->getData();

        $user->setEnabled(false);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }

        $this->mailer->sendConfirmationEmailMessage($user);
        $this->session->set('marlinc_user_send_confirmation_email/email', $user->getEmail());

        $event->setResponse($this->redirectAfterEmailSent($event));
    }

    /**
     * @param FormEvent $event
     * @return RedirectResponse
     */
    abstract protected function redirectAfterEmailSent(FormEvent $event): RedirectResponse;
}
