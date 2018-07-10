<?php

namespace Marlinc\UserBundle\Security\Authenticator;

use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Marlinc\UserBundle\Entity\User;
use Marlinc\UserBundle\Manager\Marlinc1UserLoader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Authenticate users logged in into any Marlinc1 application by verifying the access token
 * send with the request.
 *
 * @package Marlinc\UserBundle\Security\Authenticator
 */
class Marlinc1Authenticator extends AbstractGuardAuthenticator
{
    use TargetPathTrait;
    use ConfigurableRoutesTrait;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var TokenExtractorInterface
     */
    private $tokenExtractor;

    /**
     * @var TokenStorageInterface
     */
    private $preAuthenticationTokenStorage;

    /**
     * @var Marlinc1UserLoader
     */
    private $userLoader;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * Marlinc1Authenticator constructor.
     * @param EventDispatcherInterface $dispatcher
     * @param TokenExtractorInterface $tokenExtractor
     * @param TokenStorageInterface $preAuthenticationTokenStorage
     * @param Marlinc1UserLoader $userLoader
     * @param EntityManager $em
     */
    public function __construct(EventDispatcherInterface $dispatcher, TokenExtractorInterface $tokenExtractor, TokenStorageInterface $preAuthenticationTokenStorage, Marlinc1UserLoader $userLoader, EntityManager $em)
    {
        $this->dispatcher = $dispatcher;
        $this->tokenExtractor = $tokenExtractor;
        $this->preAuthenticationTokenStorage = $preAuthenticationTokenStorage;
        $this->userLoader = $userLoader;
        $this->em = $em;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new Response('Auth parameter required', 401);
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        // Look for the required token in the request header
        return false !== $this->tokenExtractor->extract($request);
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        if (false === ($authToken = $this->tokenExtractor->extract($request))) {
            return false;
        }

        // Try to load user data from marlinc DBs
        $credentials = $this->userLoader->findUserByToken($authToken);

        if ($credentials !== null) {
            return $credentials;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $userProvider->loadUserByUsername($credentials['email']);

        if ($user instanceof UserInterface) {
            // TODO: Emit event (new user already exists) -> Add flash message for new login
            return $user;
        }

        // Create user, if not found
        $user = User::createFromLegacyAccount($credentials);

        $this->em->persist($user);
        $this->em->flush();

        // TODO: Emit event -> Add flash message, assign to client
        // TODO: Force user to reset password

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($credentials === false) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // Just continue with the current request.
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // TODO: Add flash message and redirect to normal login form.
        return new Response(
            // this contains information about *why* authentication failed
            // use it, or return your own message
            strtr($exception->getMessageKey(), $exception->getMessageData()),
            401
        );
    }
}