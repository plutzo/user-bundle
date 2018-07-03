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
     * @var string
     */
    protected $impersonating;

    /**
     * @var string
     */
    protected $avatar;

    /**
     * GlobalVariables constructor.
     *
     * @param string $impersonating
     * @param string $avatar
     */
    public function __construct(string $impersonating, string $avatar)
    {
        $this->impersonating = $impersonating;
        $this->avatar = $avatar;
    }

    /**
     * @return string
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
