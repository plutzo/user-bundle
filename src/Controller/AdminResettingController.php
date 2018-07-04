<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\Controller;

use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminResettingController extends AbstractResettingController
{
    /**
     * @inheritDoc
     */
    protected function redirectBeforeRequest(): RedirectResponse
    {
        return new RedirectResponse($this->get('router')->generate('sonata_admin_dashboard'));
    }

    /**
     * @inheritDoc
     */
    protected function renderRequest(): Response
    {
        return $this->render('@MarlincUser/Admin/Security/Resetting/request.html.twig', [
            'base_template' => $this->get('sonata.admin.global_template_registry')->getTemplate('layout'),
            'admin_pool' => $this->get('sonata.admin.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function redirectAfterSendEmail(Request $request, string $username): RedirectResponse
    {
        return new RedirectResponse($this->generateUrl('marlinc_user_admin_resetting_check_email', [
            'username' => $username,
        ]));
    }

    /**
     * @inheritDoc
     */
    protected function redirectCheckEmailNoUser(Request $request): RedirectResponse
    {
        return new RedirectResponse($this->generateUrl('marlinc_user_admin_resetting_request'));
    }

    /**
     * @inheritDoc
     */
    protected function renderCheckEmail(Request $request, $tokenLifetime): Response
    {
        return $this->render('@MarlincUser/Admin/Security/Resetting/checkEmail.html.twig', [
            'base_template' => $this->get('sonata.admin.global_template_registry')->getTemplate('layout'),
            'admin_pool' => $this->get('sonata.admin.pool'),
            'tokenLifetime' => $tokenLifetime,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function redirectBeforeReset(Request $request): ?RedirectResponse
    {
        return new RedirectResponse($this->get('router')->generate('sonata_admin_dashboard'));
    }

    /**
     * @inheritDoc
     */
    protected function redirectAfterReset(Request $request): ?RedirectResponse
    {
        // TODO: Flash messages per listener?
        $message = $this->get('translator')->trans('resetting.flash.success', [], 'MarlincUserBundle');
        $this->addFlash('success', $message);

        return new RedirectResponse($this->generateUrl('sonata_admin_dashboard'));
    }

    /**
     * @inheritDoc
     */
    protected function renderReset(FormView $form, string $token): Response
    {
        return $this->render('@MarlincUser/Admin/Security/Resetting/reset.html.twig', [
            'token' => $token,
            'form' => $form,
            'base_template' => $this->get('sonata.admin.global_template_registry')->getTemplate('layout'),
            'admin_pool' => $this->get('sonata.admin.pool'),
        ]);
    }
}
