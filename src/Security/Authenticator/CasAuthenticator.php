<?php

namespace Marlinc\UserBundle\Security\Authenticator;

use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Marlinc\UserBundle\Entity\User;
use Marlinc\UserBundle\Event\UserEvents;
use Marlinc\UserBundle\Event\UserImportEvent;
use Marlinc\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CasAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var string
     */
    private $casLoginUrl;

    /**
     * @var string
     */
    private $casValidationUrl;

    /**
     * @var string
     */
    private $ticketParameterName;

    /**
     * @var string
     */
    private $serviceParameterName;

    /**
     * @var string
     */
    private $userAttributeName;

    /**
     * @var string
     */
    private $xmlNamespace;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * CasAuthenticator constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param UserManagerInterface $userManager
     * @param $serverUrl
     * @param $xmlNamespace
     * @param $ticketParameterName
     * @param $serviceParameterName
     * @param $userAttributeName
     */
    public function __construct(EventDispatcherInterface $eventDispatcher,
                                UserManagerInterface $userManager,
                                $serverUrl,
                                $xmlNamespace,
                                $ticketParameterName,
                                $serviceParameterName,
                                $userAttributeName)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->casLoginUrl = $serverUrl.'/login';
        $this->casValidationUrl = $serverUrl.'/serviceValidate';
        $this->ticketParameterName = $ticketParameterName;
        $this->serviceParameterName = $serviceParameterName;
        $this->userAttributeName = $userAttributeName;
        $this->xmlNamespace = $xmlNamespace;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        // Redirect to the CAS authentication form.
        return new RedirectResponse($this->casLoginUrl.'?'.$this->serviceParameterName.'='.urlencode($request->getUri()));
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        // try to authenticate only if required query parameter exists.
        return (bool) $request->get($this->ticketParameterName);
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
        $url = $this->casValidationUrl.'?'.$this->ticketParameterName.'='.
            $request->get($this->ticketParameterName).'&'.
            $this->serviceParameterName.'='.urlencode($this->removeCasTicket($request->getUri()));

        $client = new Client();
        $response = $client->request('GET', $url, $this->options);

        $string = $response->getBody()->getContents();

        $xml = new \SimpleXMLElement($string, 0, false, $this->xmlNamespace, true);

        if (isset($xml->authenticationSuccess)) {
            return (array) $xml->authenticationSuccess;
        }
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (isset($credentials[$this->userAttributeName])) {
            $user = $userProvider->loadUserByUsername($credentials[$this->userAttributeName]);

            if ($user instanceof UserInterface) {
                return $user;
            }

            // Create user, if not found
            $user = User::createFromCasAccount($credentials);

            $this->userManager->updateUser($user);

            $event = new UserImportEvent($user, $credentials);
            $this->eventDispatcher->dispatch(UserEvents::SECURITY_CAS_IMPORT, $event);
            // TODO: Add event listener -> Add flash message, assign to client
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // Not in use, because authentication is managed by external service.
        return true;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // If authentication was successful, redirect to the current URI with
        // the ticket parameter removed so that it is hidden from end-users.
        if ($request->query->has($this->ticketParameterName)) {
            return new RedirectResponse($this->removeCasTicket($request->getUri()));
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
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

    /**
     * Strip the CAS 'ticket' parameter from a uri.
     *
     * @param string $uri
     * @return string
     */
    protected function removeCasTicket(string $uri) {
        $parsed_url = parse_url($uri);
        // If there are no query parameters, then there is nothing to do.
        if (empty($parsed_url['query'])) {
            return $uri;
        }
        parse_str($parsed_url['query'], $query_params);
        // If there is no 'ticket' parameter, there is nothing to do.
        if (!isset($query_params[$this->ticketParameterName])) {
            return $uri;
        }
        // Remove the ticket parameter and rebuild the query string.
        unset($query_params[$this->ticketParameterName]);
        if (empty($query_params)) {
            unset($parsed_url['query']);
        } else {
            $parsed_url['query'] = http_build_query($query_params);
        }

        // Rebuild the URI from the parsed components.
        // Source: https://secure.php.net/manual/en/function.parse-url.php#106731
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}
