<?php
/**
 * Created by PhpStorm.
 * User: em
 * Date: 31.07.18
 * Time: 15:17
 */

namespace Marlinc\UserBundle\Traits;


use Marlinc\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Marlinc\EntityBundle\BlameableSoftDelete\Annotation as Marlinc;

trait BlameableSoftDeleteEntity
{
    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="Marlinc\UserBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     * @Marlinc\Blameable
     */
    private $deletedBy;

    /**
     * @return User|null
     */
    public function getDeletedBy(): ?User
    {
        return $this->deletedBy;
    }

    /**
     * @param User|null $deletedBy
     * @return $this
     */
    public function setDeletedBy(User $deletedBy = null)
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }
}