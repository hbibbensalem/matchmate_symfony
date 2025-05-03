<?php

namespace App\Repository;

use App\Entity\Suivi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SuiviRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Suivi::class);
    }
    public function findSuivisByPremiumUsers(): array
    {
        return $this->createQueryBuilder('s')
            ->join('s.id_user', 'u')
            ->where('u.is_premium = :isPremium')
            ->setParameter('isPremium', '1')
            ->getQuery()
            ->getResult();
    }
    // Add custom methods as needed
}