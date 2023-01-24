<?php

namespace Marlinc\UserBundle\Action;

use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Twig\Environment;

class CheckEmailAction
{
    private Environment $twig;
    private TemplateRegistryInterface $templateRegistry;
    private ResetPasswordHelperInterface $resetPasswordHelper;

    public function __construct(Environment $twig, TemplateRegistryInterface $templateRegistry,
                                ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->twig = $twig;
        $this->templateRegistry = $templateRegistry;
        $this->resetPasswordHelper = $resetPasswordHelper;
    }


    public function __invoke(Request $request): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession($request->getSession()))) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return new Response($this->twig->render('@MarlincUser/Admin/Security/Resetting/checkEmail.html.twig', [
            'resetToken' => $resetToken,
            'base_template' => $this->templateRegistry->getTemplate('layout'),
        ]));
    }

    private function getTokenObjectFromSession(SessionInterface $session): ?ResetPasswordToken
    {
        return $session->get('ResetPasswordToken');
    }
}
