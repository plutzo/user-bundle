<?php

namespace Marlinc\UserBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var UserPasswordEncoder
     */
    private $passwordEncoder;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var string
     */
    private $redirectRoute;

    /**
     * @var string
     */
    private $loginRoute;

    /**
     * LoginFormAuthenticator constructor.
     * @param RouterInterface $router
     * @param UserPasswordEncoder $passwordEncoder
     * @param CsrfTokenManagerInterface $csrfTokenManager
     */
    public function __construct(RouterInterface $router, UserPasswordEncoder $passwordEncoder, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->redirectRoute = 'homepage';
        $this->loginRoute = 'login';
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        // Only try to authenticate if current path matches login route
        if ($request->getBaseUrl().$request->getPathInfo() != $this->router->generate($this->loginRoute) || !$request->isMethod('POST')) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getCredentials(Request $request)
    {
        $username = $request->request->get('_username');
        $password = $request->request->get('_password');
        $csrfToken = $request->request->get('_csrf_token');

        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $csrfToken))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token.');
        }

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $username
        );

        return [
            'username' => $username,
            'password' => $password,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['username']);
    }

    /**
     * @inheritdoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // if the user hit a secure page and start() was called, this was
        // the URL they were on, and probably where you want to redirect to
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);
        if (!$targetPath) {
            $targetPath = $this->router->generate($this->redirectRoute);
        }
        return new RedirectResponse($targetPath);
    }

    /**
     * @inheritdoc
     */
    protected function getLoginUrl()
    {
        return $this->router->generate($this->loginRoute);
    }

    /**
     * @param string $redirectRoute
     * @return LoginFormAuthenticator
     */
    public function setRedirectRoute(string $redirectRoute): LoginFormAuthenticator
    {
        $this->redirectRoute = $redirectRoute;

        return $this;
    }

    /**
     * @param string $loginRoute
     * @return LoginFormAuthenticator
     */
    public function setLoginRoute(string $loginRoute): LoginFormAuthenticator
    {
        $this->loginRoute = $loginRoute;

        return $this;
    }
}