<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\Controller;

use Marlinc\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;


class AdminSecurityController extends AbstractSecurityController
{
    /**
     * @inheritdoc
     */
    protected function renderLogin(SessionInterface $session, string $csrfToken, AuthenticationException $error = null): Response
    {
        return $this->render('@MarlincUser/Admin/Security/login.html.twig', [
            'admin_pool' => $this->get('sonata.admin.pool'),
            'base_template' => $this->get('sonata.admin.global_template_registry')->getTemplate('layout'),
            'csrf_token' => $csrfToken,
            'error' => $error,
            'last_username' => (null === $session) ? '' : $session->get(Security::LAST_USERNAME),
            'reset_route' => $this->generateUrl('marlinc_user_admin_resetting_request'),
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function redirectBeforeLogin(Request $request): ?RedirectResponse
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $refererUri = $request->server->get('HTTP_REFERER');

            return $this->redirect($refererUri && $refererUri != $request->getUri() ? $refererUri : $this->generateUrl('sonata_admin_dashboard'));
        }

        if ($this->getUser() instanceof UserInterface) {
            // TODO return 403 Exception instead.
            $this->addFlash('sonata_user_error', 'sonata_user_already_authenticated');
            $url = $this->generateUrl('sonata_admin_dashboard');

            return $this->redirect($url);
        }
    }
}
