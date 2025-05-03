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

    public function countTodayUsers(\DateTimeInterface $start, \DateTimeInterface $end): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)') // Utilisez 'id' au lieu de 'id_user' sauf si votre colonne s'appelle vraiment id_user
            ->where('u.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
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

    public function countTodayLogins(): int
{
    $todayStart = new \DateTimeImmutable('today');
    $todayEnd = $todayStart->modify('+1 day');

    return $this->createQueryBuilder('u')
        ->select('COUNT(u.id_user)')
        ->where('u.lastLoginAt BETWEEN :start AND :end')
        ->setParameter('start', $todayStart)
        ->setParameter('end', $todayEnd)
        ->getQuery()
        ->getSingleScalarResult();
}

public function countNewUsersToday(): int
{
    $todayStart = new \DateTimeImmutable('today');
    $todayEnd = $todayStart->modify('+1 day');

    return $this->createQueryBuilder('u')
        ->select('COUNT(u.id_user)')
        ->where('u.createdAt BETWEEN :start AND :end')
        ->setParameter('start', $todayStart)
        ->setParameter('end', $todayEnd)
        ->getQuery()
        ->getSingleScalarResult();
}

public function findPremiumPlayers(): array
{
    return $this->createQueryBuilder('u')
        ->andWhere('u.is_premium = :isPremium')
        ->andWhere('u.role = :role')
        ->setParameter('isPremium', '1')
        ->setParameter('role', 'PLAYER')
        ->orderBy('u.nom_user', 'ASC')
        ->addOrderBy('u.prenom_user', 'ASC')
        ->getQuery()
        ->getResult();
}

}