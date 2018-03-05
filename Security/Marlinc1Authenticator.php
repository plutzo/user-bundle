<?php

namespace Marlinc\UserBundle\Security;

use Marlinc\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Http\HttpUtils;

class Marlinc1Authenticator implements SimplePreAuthenticatorInterface
{
    protected $httpUtils;
    
    public function __construct(HttpUtils $httpUtils)
    {
        $this->httpUtils = $httpUtils;
    }
    
    public function createToken(Request $request, $providerKey)
    {
        // Look for an apikey query parameter
        $apiKey = $request->query->get('apikey');

        if ($apiKey === '' || $apiKey === null) {
            throw new BadCredentialsException();
        }

        return new PreAuthenticatedToken(
            'anon.',
            $apiKey,
            $providerKey
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        $valid = ($token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey);
        return $valid;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if(!$userProvider instanceof Marlinc1UserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    "The user provider must be an instance of marlincUserProvider % was given",
                    get_class($userProvider)
                )
            );
        }

        $apiKey = $token->getCredentials();
        $username = $userProvider->getUsernameForApiKey($apiKey);
        $user = $token->getUser();

        if ($user instanceof User) {
            return new PreAuthenticatedToken($user, $apiKey, $providerKey, ['ROLE_USER']);
        }
        
        if (!$username) {
            throw new AuthenticationException(
                sprintf('ApiKey (%s) does not exist.', get_class($apiKey))
            );
        }

        $user = $userProvider->loadUserByUsername($username);

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            ['ROLE_USER']
        );
    }
    
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response(
            // this contains information about *why* authentication failed
            // use it, or return your own message
            strtr($exception->getMessageKey(), $exception->getMessageData()),
            401
        );
    }
}