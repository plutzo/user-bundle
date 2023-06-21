<?php

namespace Marlinc\UserBundle\Action;

use Marlinc\UserBundle\Entity\UserInterface;
use Marlinc\UserBundle\Entity\UserManagerInterface;
use Marlinc\UserBundle\Form\Type\ResettingFormType;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\HttpUtils;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Twig\Environment;

class ResetPasswordAction
{
    private Environment $twig;
    private RouterInterface $router;
    private FormFactoryInterface $formFactory;
    private TemplateRegistryInterface $templateRegistry;
    private ResetPasswordHelperInterface $resetPasswordHelper;
    private UserManagerInterface $userManager;

    public function __construct(Environment $twig, RouterInterface $router, FormFactoryInterface $formFactory,
                                TemplateRegistryInterface $templateRegistry,
                                ResetPasswordHelperInterface $resetPasswordHelper, UserManagerInterface $userManager)
    {
        $this->twig = $twig;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->templateRegistry = $templateRegistry;
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->userManager = $userManager;
    }


    public function __invoke(Request $request, string $token = null): Response
    {
        $session = $request->getSession();

        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($session, $token);

            return new RedirectResponse($this->router->generate('marlinc_user.admin.reset_password'));
        }

        $token = $this->getTokenFromSession($session);

        if (null === $token) {
            throw new NotFoundHttpException('No reset password token found in the URL or in the session.');
        }

        try {
            /** @var UserInterface $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $session->getFlashBag()->add('reset_password_error', sprintf(
                'There was a problem validating your reset request - %s',
                $e->getReason()
            ));

            return new RedirectResponse($this->router->generate( 'marlinc_user.admin.forgot_password_request'));
        }

        // The token is valid; allow the user to change their password.
        $form = $this->formFactory->create(ResettingFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode(hash) the plain password, and set it.
            $user->setPlainPassword($form->get('plainPassword')->getData());
            $this->userManager->updatePassword($user);
            $this->userManager->save($user);

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset($session);

            return new RedirectResponse($this->router->generate( 'marlinc_user.admin.login'));
        }

        return new Response($this->twig->render('@MarlincUser/Admin/Security/Resetting/reset.html.twig', [
            'form' => $form->createView(),
            'base_template' => $this->templateRegistry->getTemplate('layout'),
        ]));
    }

    private function storeTokenInSession(SessionInterface $session, string $token): void
    {
        $session->set('ResetPasswordPublicToken', $token);
    }

    private function getTokenFromSession(SessionInterface $session): ?string
    {
        return $session->get('ResetPasswordPublicToken');
    }

    private function cleanSessionAfterReset(SessionInterface $session): void
    {
        $session->remove('ResetPasswordPublicToken');
        $session->remove('ResetPasswordCheckEmail');
        $session->remove('ResetPasswordToken');
    }
}
