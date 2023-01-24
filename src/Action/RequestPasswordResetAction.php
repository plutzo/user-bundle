<?php

namespace Marlinc\UserBundle\Action;

use Marlinc\UserBundle\Entity\UserManagerInterface;
use Marlinc\UserBundle\Form\Type\ResetPasswordRequestFormType;
use Marlinc\UserBundle\Util\EmailCanonicalizer;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Twig\Environment;

final class RequestPasswordResetAction
{
    private Environment $twig;
    private MailerInterface $mailer;
    private HttpUtils $httpUtils;
    private FormFactoryInterface $formFactory;
    private TemplateRegistryInterface $templateRegistry;
    private ResetPasswordHelperInterface $resetPasswordHelper;
    private UserManagerInterface $userManager;
    private TranslatorInterface $translator;

    private array $fromEmail;
    private string $template;

    public function __construct(Environment                  $twig, MailerInterface $mailer, HttpUtils $httpUtils,
                                FormFactoryInterface         $formFactory, TemplateRegistryInterface $templateRegistry,
                                ResetPasswordHelperInterface $resetPasswordHelper, UserManagerInterface $userManager,
                                TranslatorInterface          $translator, array $fromEmail, string $template)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->httpUtils = $httpUtils;
        $this->formFactory = $formFactory;
        $this->templateRegistry = $templateRegistry;
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->userManager = $userManager;
        $this->translator = $translator;
        $this->fromEmail = $fromEmail;
        $this->template = $template;
    }


    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail($request, $form->get('email')->getData());
        }

        return new Response($this->twig->render('@MarlincUser/Admin/Security/Resetting/request.html.twig', [
            'form' => $form->createView(),
            'base_template' => $this->templateRegistry->getTemplate('layout'),
        ]));
    }

    private function processSendingPasswordResetEmail(Request $request, string $email): RedirectResponse
    {
        $user = $this->userManager->findUserByEmail(EmailCanonicalizer::canonicalize($email));

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->httpUtils->createRedirectResponse($request, 'marlinc_user.admin.check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            // $this->addFlash('reset_password_error', sprintf(
            //     'There was a problem handling your password reset request - %s',
            //     $e->getReason()
            // ));

            return $this->httpUtils->createRedirectResponse($request, 'marlinc_user.admin.check_email');
        }

        $fromName = current($this->fromEmail);
        $fromAddress = current(array_keys($this->fromEmail));

        $email = (new TemplatedEmail())
            ->from(new Address($fromAddress,$fromName))
            ->to($user->getEmail())
            ->subject($this->translator->trans('marlinc_user_password_reset_subject', [], 'MarlincUserBundle'))
            ->htmlTemplate($this->template)
            ->context([
                'resetToken' => $resetToken,
                'user' => $user
            ])
        ;

        $this->mailer->send($email);

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($request->getSession(), $resetToken);

        return $this->httpUtils->createRedirectResponse($request, 'marlinc_user.admin.check_email');
    }

    private function setTokenObjectInSession(Session $session, ResetPasswordToken $token): void
    {
        $token->clearToken();

        $session->set('ResetPasswordToken', $token);
    }
}
