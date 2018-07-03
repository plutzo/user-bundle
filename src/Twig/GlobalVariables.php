<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\Twig;

use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * GlobalVariables.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class GlobalVariables
{
    /**
     * @var array
     */
    protected $impersonating;

    /**
     * @var string
     */
    protected $avatar;

    /**
     * @var AdminInterface
     */
    private $userAdmin;

    /**
     * GlobalVariables constructor.
     *
     * @param array $impersonating
     * @param string $avatar
     * @param AdminInterface $userAdmin
     */
    public function __construct(array $impersonating, string $avatar, AdminInterface $userAdmin)
    {
        $this->impersonating = $impersonating;
        $this->avatar = $avatar;
        $this->userAdmin = $userAdmin;
    }

    /**
     * @return array
     */
    public function getImpersonating()
    {
        return $this->impersonating;
    }

    /**
     * @return string
     */
    public function getDefaultAvatar()
    {
        return $this->avatar;
    }

    /**
     * @return AdminInterface
     */
    public function getUserAdmin()
    {
        return $this->userAdmin;
    }
}
