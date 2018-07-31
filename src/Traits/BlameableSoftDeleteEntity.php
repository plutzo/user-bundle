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

trait BlameableSoftDeleteEntity
{
    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="Marlinc\UserBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
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
     * @param User $deletedBy
     * @return $this
     */
    public function setDeletedBy(User $deletedBy)
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }
}