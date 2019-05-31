<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"like" = "LikeNotification"})
 */
abstract class Notification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     *
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    private $seen;

    public function __construct()
    {
        $this->seen = false;
    }

    /**
     * @return bool
     */
    public function isSeen(): bool
    {
        return $this->seen;
    }

    /**
     * @param bool $seen
     *
     * @return Notification
     */
    public function setSeen(bool $seen): Notification
    {
        $this->seen = $seen;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Notification
     */
    public function setUser(User $user): Notification
    {
        $this->user = $user;

        return $this;
    }
}