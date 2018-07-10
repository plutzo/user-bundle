<?php
/**
 * Created by PhpStorm.
 * User: em
 * Date: 10.07.18
 * Time: 14:18
 */

namespace Marlinc\UserBundle\Security\Authenticator;


use Symfony\Component\Routing\RouterInterface;

trait ConfigurableRoutesTrait
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $redirectRoute;

    /**
     * @var string
     */
    private $loginRoute;

    /**
     * @var string|null
     */
    private $host;

    /**
     * @param string $name
     * @return string
     */
    private function generateRoute(string $name): string
    {
        if ($this->host !== null) {
            return $this->router->generate($name, [
                'host' => $this->host
            ]);
        } else {
            return $this->router->generate($name);
        }
    }

    /**
     * @param string $redirectRoute
     * @return self
     */
    public function setRedirectRoute(string $redirectRoute)
    {
        $this->redirectRoute = $redirectRoute;

        return $this;
    }

    /**
     * @param string $loginRoute
     * @return self
     */
    public function setLoginRoute(string $loginRoute)
    {
        $this->loginRoute = $loginRoute;

        return $this;
    }

    /**
     * @param null|string $host
     * @return self
     */
    public function setHost(?string $host)
    {
        $this->host = $host;

        return $this;
    }
}