<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LikeNotificationRepository")
 */
class LikeNotification extends Notification
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MicroPost")
     *
     * @var MicroPost
     */
    private $microPost;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     *
     * @var User
     */
    private $likedBy;

    /**
     * @return MicroPost
     */
    public function getMicroPost(): MicroPost
    {
        return $this->microPost;
    }

    /**
     * @param MicroPost $microPost
     *
     * @return LikeNotification
     */
    public function setMicroPost(MicroPost $microPost): LikeNotification
    {
        $this->microPost = $microPost;

        return $this;
    }

    /**
     * @return User
     */
    public function getLikedBy(): User
    {
        return $this->likedBy;
    }

    /**
     * @param User $likedBy
     *
     * @return LikeNotification
     */
    public function setLikedBy(User $likedBy): LikeNotification
    {
        $this->likedBy = $likedBy;

        return $this;
    }
}