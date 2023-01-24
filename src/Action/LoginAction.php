<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\Action;

use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Marlinc\UserBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class LoginAction
{
    private Environment $twig;
    private UrlGeneratorInterface $urlGenerator;
    private AuthenticationUtils $authenticationUtils;
    private Pool $adminPool;
    private TemplateRegistryInterface $templateRegistry;
    private TokenStorageInterface $tokenStorage;
    private TranslatorInterface $translator;

    public function __construct(
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        AuthenticationUtils $authenticationUtils,
        Pool $adminPool,
        TemplateRegistryInterface $templateRegistry,
        TokenStorageInterface $tokenStorage,
        TranslatorInterface $translator
    ) {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->authenticationUtils = $authenticationUtils;
        $this->adminPool = $adminPool;
        $this->templateRegistry = $templateRegistry;
        $this->tokenStorage = $tokenStorage;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): Response
    {
        if ($this->isAuthenticated()) {
            $request->getSession()->getFlashBag()->add(
                'marlinc_user_error',
                $this->translator->trans('marlinc_user_already_authenticated', [], 'MarlincUserBundle')
            );

            return new RedirectResponse($this->urlGenerator->generate('sonata_admin_dashboard'));
        }

        return new Response($this->twig->render('@MarlincUser/Admin/Security/login.html.twig', [
            'admin_pool' => $this->adminPool,
            'base_template' => $this->templateRegistry->getTemplate('layout'),
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'reset_route' => $this->urlGenerator->generate('marlinc_user.admin.forgot_password_request'),
        ]));
    }

    private function isAuthenticated(): bool
    {
        $token = $this->tokenStorage->getToken();

        return $token !== null && $token->getUser() instanceof UserInterface;
    }
}
