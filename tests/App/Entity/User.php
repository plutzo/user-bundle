<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Tests\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marlinc\UserBundle\Entity\BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="user__user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
