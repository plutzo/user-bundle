<?php

namespace Marlinc\UserBundle\EventListener;

use Marlinc\UserBundle\Event\UserEvents;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Add success flash messages for certain user events.
 *
 * @package Marlinc\UserBundle\EventListener
 */
class FlashListener implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private static $successMessages = [
        UserEvents::CHANGE_PASSWORD_COMPLETED => 'change_password.flash.success',
        UserEvents::PROFILE_EDIT_COMPLETED => 'profile.flash.updated',
        UserEvents::REGISTRATION_COMPLETED => 'registration.flash.user_created',
        UserEvents::RESETTING_RESET_COMPLETED => 'resetting.flash.success',
    ];

    /**
     * @var Session
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * FlashListener constructor.
     *
     * @param Session             $session
     * @param TranslatorInterface $translator
     */
    public function __construct(Session $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            UserEvents::CHANGE_PASSWORD_COMPLETED => 'addSuccessFlash',
            UserEvents::PROFILE_EDIT_COMPLETED => 'addSuccessFlash',
            UserEvents::REGISTRATION_COMPLETED => 'addSuccessFlash',
            UserEvents::RESETTING_RESET_COMPLETED => 'addSuccessFlash',
        ];
    }

    /**
     * @param Event  $event
     * @param string $eventName
     */
    public function addSuccessFlash(Event $event, $eventName)
    {
        if (!isset(self::$successMessages[$eventName])) {
            throw new \InvalidArgumentException('This event does not correspond to a known flash message');
        }

        $this->session->getFlashBag()->add('success', $this->trans(self::$successMessages[$eventName]));
    }

    /**
     * @param string$message
     * @param array $params
     *
     * @return string
     */
    private function trans($message, array $params = [])
    {
        return $this->translator->trans($message, $params, 'MarlincUserBundle');
    }
}
