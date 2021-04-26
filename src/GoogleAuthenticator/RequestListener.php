<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\GoogleAuthenticator;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RequestListener
{
    /**
     * @var Authenticator
     */
    protected $helper;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @param Authenticator $helper
     * @param TokenStorageInterface $tokenStorage
     * @param EngineInterface $templating
     */
    public function __construct(Authenticator $helper, TokenStorageInterface $tokenStorage, EngineInterface $templating)
    {
        $this->helper = $helper;
        $this->tokenStorage = $tokenStorage;
        $this->templating = $templating;
    }

    /**
     * @param ResponseEvent $event
     */
    public function onCoreRequest(ResponseEvent $event): void
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return;
        }

        if (!$token instanceof UsernamePasswordToken) {
            return;
        }

        $key = $this->helper->getSessionKey($token);
        $request = $event->getRequest();
        $session = $event->getRequest()->getSession();
        $user = $token->getUser();

        if (!$session->has($key)) {
            return;
        }

        if (true === $session->get($key)) {
            return;
        }

        $state = 'init';
        if ('POST' == $request->getMethod()) {
            if (true == $this->helper->checkCode($user, $request->get('_code'))) {
                $session->set($key, true);

                return;
            }

            $state = 'error';
        }

        $event->setResponse($this->templating->renderResponse('@MarlincUser/Admin/Security/login.html.twig', [
            'base_template' => '@SonataAdmin/standard_layout.html.twig',
            'error' => [],
            'state' => $state,
            'two_step_submit' => true,
        ]));
    }
}
