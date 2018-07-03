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
     * GlobalVariables constructor.
     *
     * @param array $impersonating
     * @param string $avatar
     */
    public function __construct(array $impersonating, string $avatar)
    {
        $this->impersonating = $impersonating;
        $this->avatar = $avatar;
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
}
