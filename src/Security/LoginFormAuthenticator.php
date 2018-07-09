<?php

namespace Marlinc\UserBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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
    private $redirectPath;

    /**
     * @var string
     */
    private $loginPath;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * LoginFormAuthenticator constructor.
     * @param RouterInterface $router
     * @param UserPasswordEncoder $passwordEncoder
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param RequestStack $requestStack
     */
    public function __construct(RouterInterface $router, UserPasswordEncoder $passwordEncoder, CsrfTokenManagerInterface $csrfTokenManager, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->requestStack = $requestStack;
        $this->redirectPath = $this->router->generate('login');
        $this->loginPath = $this->router->generate('login');
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        // Only try to authenticate if current path matches login route
        if ($request->getBaseUrl().$request->getPathInfo() != $this->loginPath || !$request->isMethod('POST')) {
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
            $targetPath = $this->redirectPath;
        }
        return new RedirectResponse($targetPath);
    }

    /**
     * @inheritdoc
     */
    protected function getLoginUrl()
    {
        return $this->loginPath;
    }

    /**
     * @param string $redirectRoute
     * @param bool $withHost
     * @return LoginFormAuthenticator
     */
    public function setRedirectRoute(string $redirectRoute, bool $withHost = false): LoginFormAuthenticator
    {
        $requirements = [];
        if ($withHost) {
            $requirements['host'] = $this->requestStack->getCurrentRequest()->getHost();
        }

        $this->redirectPath = $this->router->generate($redirectRoute, $requirements);

        return $this;
    }

    /**
     * @param string $loginRoute
     * @param bool $withHost
     * @return LoginFormAuthenticator
     */
    public function setLoginRoute(string $loginRoute, bool $withHost = false): LoginFormAuthenticator
    {
        $requirements = [];
        if ($withHost) {
            $requirements['host'] = $this->requestStack->getCurrentRequest()->getHost();
        }

        $this->loginPath = $this->router->generate($loginRoute, $requirements);

        return $this;
    }
}