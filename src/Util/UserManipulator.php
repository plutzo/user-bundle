<?php
/**
 * Created by PhpStorm.
 * User: elias
 * Date: 15.03.17
 * Time: 15:30
 */

namespace Marlinc\UserBundle\Util;

use Marlinc\UserBundle\Entity\User;
use Marlinc\UserBundle\Event\UserEvent;
use Marlinc\UserBundle\Event\UserEvents;
use Marlinc\UserBundle\Model\UserInterface;
use Marlinc\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Executes some manipulations on the users.
 *
 * @author Elias Müller
 */
class UserManipulator
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var null|Request
     */
    private $request;

    /**
     * UserManipulator constructor.
     *
     * @param UserManagerInterface     $userManager
     * @param EventDispatcherInterface $dispatcher
     * @param RequestStack             $requestStack
     */
    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $dispatcher, RequestStack $requestStack)
    {
        $this->userManager = $userManager;
        $this->dispatcher = $dispatcher;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * Creates a user and returns it.
     *
     * @param string $email
     * @param string $password
     * @param $firstname
     * @param $lastname
     * @param bool $active
     * @param bool $superadmin
     *
     * @return UserInterface
     */
    public function create($email, $password, $firstname, $lastname, $active, $superadmin)
    {
        $user = $this->userManager->createUser();
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled((bool) $active);
        $user->setSuperAdmin((bool) $superadmin);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->request);
        $this->dispatcher->dispatch(UserEvents::USER_CREATED, $event);

        return $user;
    }

    /**
     * Activates the given user.
     *
     * @param string $email
     */
    public function activate($email)
    {
        $user = $this->findUserByEmailOrThrowException($email);
        $user->setEnabled(true);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->request);
        $this->dispatcher->dispatch(UserEvents::USER_ACTIVATED, $event);
    }

    /**
     * Deactivates the given user.
     *
     * @param string $email
     */
    public function deactivate($email)
    {
        $user = $this->findUserByEmailOrThrowException($email);
        $user->setEnabled(false);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->request);
        $this->dispatcher->dispatch(UserEvents::USER_DEACTIVATED, $event);
    }

    /**
     * Changes the password for the given user.
     *
     * @param string $email
     * @param string $password
     */
    public function changePassword($email, $password)
    {
        $user = $this->findUserByEmailOrThrowException($email);
        $user->setPlainPassword($password);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->request);
        $this->dispatcher->dispatch(UserEvents::USER_PASSWORD_CHANGED, $event);
    }

    /**
     * Promotes the given user.
     *
     * @param string $email
     */
    public function promote($email)
    {
        $user = $this->findUserByEmailOrThrowException($email);
        $user->setSuperAdmin(true);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->request);
        $this->dispatcher->dispatch(UserEvents::USER_PROMOTED, $event);
    }

    /**
     * Demotes the given user.
     *
     * @param string $email
     */
    public function demote($email)
    {
        $user = $this->findUserByEmailOrThrowException($email);
        $user->setSuperAdmin(false);
        $this->userManager->updateUser($user);

        $event = new UserEvent($user, $this->request);
        $this->dispatcher->dispatch(UserEvents::USER_DEMOTED, $event);
    }

    /**
     * Adds role to the given user.
     *
     * @param string $email
     * @param string $role
     *
     * @return bool true if role was added, false if user already had the role
     */
    public function addRole($email, $role)
    {
        $user = $this->findUserByEmailOrThrowException($email);
        if ($user->hasRole($role)) {
            return false;
        }
        $user->addRole($role);
        $this->userManager->updateUser($user);

        return true;
    }

    /**
     * Removes role from the given user.
     *
     * @param string $email
     * @param string $role
     *
     * @return bool true if role was removed, false if user didn't have the role
     */
    public function removeRole($email, $role)
    {
        $user = $this->findUserByEmailOrThrowException($email);
        if (!$user->hasRole($role)) {
            return false;
        }
        $user->removeRole($role);
        $this->userManager->updateUser($user);

        return true;
    }

    /**
     * Finds a user by his email and throws an exception if we can't find it.
     *
     * @param string $email
     *
     * @throws \InvalidArgumentException When user does not exist
     *
     * @return UserInterface
     */
    private function findUserByEmailOrThrowException($email)
    {
        $user = $this->userManager->findUserByEmail($email);

        if (!$user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" email does not exist.', $email));
        }

        return $user;
    }
}
