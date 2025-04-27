<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findUsersToReactivate(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.isActive = false') // Only inactive users
            ->andWhere('u.reactivateAt IS NOT NULL') // With a reactivation time set
            ->andWhere('u.reactivateAt <= :now') // Reactivation time has passed
            ->setParameter('now', new \DateTime()) // Current time
            ->getQuery()
            ->getResult();
    }

    public function findPendingNutritionists(): array
{
    return $this->createQueryBuilder('u')
        ->where('u.role = :role')
        ->andWhere('u.isActive = :isActive')
        ->setParameter('role', 'NUTRITIONIST')
        ->setParameter('isActive', false)
        ->orderBy('u.dateInscription', 'ASC')
        ->getQuery()
        ->getResult();
}

    // Add custom methods as needed
}