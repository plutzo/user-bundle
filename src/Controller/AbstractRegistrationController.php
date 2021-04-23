<?php

namespace Marlinc\UserBundle\Controller;

use Marlinc\UserBundle\Event\FilterUserResponseEvent;
use Marlinc\UserBundle\Event\FormEvent;
use Marlinc\UserBundle\Event\GetResponseUserEvent;
use Marlinc\UserBundle\Event\UserEvents;
use Marlinc\UserBundle\Form\Factory\FactoryInterface;
use Marlinc\UserBundle\Model\UserInterface;
use Marlinc\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller managing the registration.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
abstract class AbstractRegistrationController extends AbstractController
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var FactoryInterface
     */
    private $formFactory;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(EventDispatcherInterface $eventDispatcher, FactoryInterface $formFactory, UserManagerInterface $userManager, TokenStorageInterface $tokenStorage)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * The registration form
     *
     * @param Request $request
     *
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $user = $this->userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(UserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->createForm();
        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $this->eventDispatcher->dispatch(UserEvents::REGISTRATION_SUCCESS, $event);

                $this->userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $response = $this->redirectAfterRegister($request);
                }

                $this->eventDispatcher->dispatch(UserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }

            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch(UserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }

        return $this->renderRegister($form->createView());
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    abstract protected function redirectAfterRegister(Request $request): RedirectResponse;

    /**
     * @param FormView $form
     * @return Response
     */
    abstract protected function renderRegister(FormView $form): Response;

    /**
     * Tell the user to check their email provider.
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function checkEmailAction(Request $request)
    {
        $email = $request->getSession()->get('marlinc_user_send_confirmation_email/email');

        // No mail address found in session = confirmation mail wasn't sent.
        if (empty($email)) {
            return $this->redirectCheckEmailNoEmail($request);
        }

        $request->getSession()->remove('marlinc_user_send_confirmation_email/email');
        $user = $this->userManager->findUserByEmail($email);

        if (null === $user) {
            return $this->redirectCheckEmailNoUser($request);
        }

        return $this->renderCheckEmail($request, $user);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    abstract protected function redirectCheckEmailNoEmail(Request $request): RedirectResponse;

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    abstract protected function redirectCheckEmailNoUser(Request $request): RedirectResponse;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    abstract protected function renderCheckEmail(Request $request, UserInterface $user): Response;

    /**
     * Receive the confirmation token from user email provider, login the user.
     *
     * @param Request $request
     * @param string $token
     *
     * @return Response
     */
    public function confirmAction(Request $request, $token)
    {
        $userManager = $this->userManager;

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(UserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $response = $this->redirectAfterConfirm($request);
        }

        $this->eventDispatcher->dispatch(UserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    abstract protected function redirectAfterConfirm(Request $request): RedirectResponse;

    /**
     * Tell the user his account is now confirmed.
     *
     * @param Request $request
     * @return Response
     */
    public function confirmedAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->renderConfirmed($request, $user);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    abstract protected function renderConfirmed(Request $request, UserInterface $user): Response;

    /**
     * @param SessionInterface $session
     * @return string|null
     */
    protected function getTargetUrlFromSession(SessionInterface $session)
    {
        $key = sprintf('_security.%s.target_path', $this->tokenStorage->getToken()->getProviderKey());

        if ($session->has($key)) {
            return $session->get($key);
        }

        return null;
    }
}
