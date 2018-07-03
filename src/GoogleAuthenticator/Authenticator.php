<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\GoogleAuthenticator;

use Google\Authenticator\GoogleAuthenticator;
use Marlinc\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Authenticator
{
    /**
     * @var string
     */
    protected $server;

    /**
     * @var GoogleAuthenticator
     */
    protected $authenticator;

    /**
     * @var string[]
     */
    private $forcedForRoles;

    /**
     * @var string[]
     */
    private $ipWhiteList;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * Authenticator constructor.
     * @param $server
     * @param GoogleAuthenticator $authenticator
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param array $forcedForRoles
     * @param array $ipWhiteList
     */
    public function __construct(
      $server,
      GoogleAuthenticator $authenticator,
      AuthorizationCheckerInterface $authorizationChecker,
      array $forcedForRoles = [],
      array $ipWhiteList = []
      ) {
        $this->server = $server;
        $this->authenticator = $authenticator;
        $this->authorizationChecker = $authorizationChecker;
        $this->forcedForRoles = $forcedForRoles;
        $this->ipWhiteList = $ipWhiteList;
    }

    /**
     * @param UserInterface $user
     * @param $code
     *
     * @return bool
     */
    public function checkCode(UserInterface $user, $code)
    {
        return $this->authenticator->checkCode($user->getTwoStepVerificationCode(), $code);
    }

    /**
     * @param UserInterface $user
     *
     * @return string
     */
    public function getUrl(UserInterface $user)
    {
        return $this->authenticator->getUrl($user->getUsername(), $this->server, $user->getTwoStepVerificationCode());
    }

    /**
     * @return string
     */
    public function generateSecret()
    {
        return $this->authenticator->generateSecret();
    }

    /**
     * @param UsernamePasswordToken $token
     *
     * @return string
     */
    public function getSessionKey(UsernamePasswordToken $token)
    {
        return sprintf('sonata_user_google_authenticator_%s_%s', $token->getProviderKey(), $token->getUsername());
    }

    /**
     * @return bool
     */
    public function needToHaveGoogle2FACode(Request $request): bool
    {
        $ip = $request->server->get('HTTP_X_FORWARDED_FOR', $request->server->get('REMOTE_ADDR'));
        if (in_array($ip, $this->ipWhiteList)) {
            return false;
        }

        foreach ($this->forcedForRoles as $role) {
            if ($this->authorizationChecker->isGranted($role)) {
                return true;
            }
        }

        return false;
    }
}
