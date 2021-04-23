<?php
/**
 * Created by PhpStorm.
 * User: em
 * Date: 04.07.18
 * Time: 13:21
 */

namespace Marlinc\UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

abstract class AbstractSecurityController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function loginAction(Request $request)
    {
        $redirect = $this->redirectBeforeLogin($request);

        if ($redirect instanceof RedirectResponse) {
            return $redirect;
        }

        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        $csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

        return $this->renderLogin($session, $csrfToken, $error);
    }

    /**
     * @param SessionInterface $session
     * @param string $csrfToken
     * @param AuthenticationException|null $error
     * @return Response
     */
    abstract protected function renderLogin(SessionInterface $session, string $csrfToken, AuthenticationException $error = null): Response;

    abstract protected function redirectBeforeLogin(Request $request): ?RedirectResponse;
}