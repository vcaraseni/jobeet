<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @param User $user
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function findUnseenByUser(User $user)
    {
        return $this->createQueryBuilder('n')
            ->where('n.user')
            ->select('COUNT(n)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}