<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Marlinc\UserBundle\Action;

use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Marlinc\UserBundle\Form\Type\ResetPasswordRequestFormType;
use Marlinc\UserBundle\Mailer\MailerInterface;
use Marlinc\UserBundle\Entity\UserManagerInterface;
use Marlinc\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

final class RequestAction
{
    private Environment $twig;

    private UrlGeneratorInterface $urlGenerator;

    private AuthorizationCheckerInterface $authorizationChecker;

    private Pool $adminPool;

    private TemplateRegistryInterface $templateRegistry;

    private FormFactoryInterface $formFactory;

    private UserManagerInterface $userManager;

    private MailerInterface $mailer;

    private TokenGeneratorInterface $tokenGenerator;

    private int $retryTtl;

    public function __construct(
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        AuthorizationCheckerInterface $authorizationChecker,
        Pool $adminPool,
        TemplateRegistryInterface $templateRegistry,
        FormFactoryInterface $formFactory,
        UserManagerInterface $userManager,
        MailerInterface $mailer,
        TokenGeneratorInterface $tokenGenerator,
        int $retryTtl
    ) {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->authorizationChecker = $authorizationChecker;
        $this->adminPool = $adminPool;
        $this->templateRegistry = $templateRegistry;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->retryTtl = $retryTtl;
    }

    public function __invoke(Request $request): Response
    {
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->urlGenerator->generate('sonata_admin_dashboard'));
        }

        $form = $this->formFactory->create(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $this->userManager->findUserByEmail($email);

            if (null !== $user && $user->isEnabled() && !$user->isPasswordRequestNonExpired($this->retryTtl) ) {
                if (null === $user->getConfirmationToken()) {
                    $user->setConfirmationToken($this->tokenGenerator->generateToken());
                }

                $this->mailer->sendResettingEmailMessage($user);
                $user->setPasswordRequestedAt(new \DateTime());
                $this->userManager->save($user);
            }

            return new RedirectResponse($this->urlGenerator->generate('marlinc_user_admin_resetting_check_email', [
                'email' => $email,
            ]));
        }

        return new Response($this->twig->render('@MarlincUser/Admin/Security/Resetting/request.html.twig', [
            'base_template' => $this->templateRegistry->getTemplate('layout'),
            'admin_pool' => $this->adminPool,
            'form' => $form->createView(),
        ]));
    }
}
