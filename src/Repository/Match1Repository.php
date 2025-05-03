<?php

namespace App\Repository;

use App\Entity\Match1;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Match1Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Match1::class);
    }

    public function getSportsCountByLocation(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.localisation', 'm.typesport', 'COUNT(m.id) as count')
            ->groupBy('m.localisation', 'm.typesport')
            ->orderBy('m.localisation', 'ASC')
            ->addOrderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getMostPopularSports(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.typesport', 'COUNT(m.id) as count')
            ->groupBy('m.typesport')
            ->orderBy('count', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function getMatchCountByStatus(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.statut', 'COUNT(m.id) as count')
            ->groupBy('m.statut')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getMatchCountByMonth(): array
{
    $results = $this->createQueryBuilder('m')
        ->select('m.date', 'COUNT(m.id) as count')
        ->getQuery()
        ->getResult();

    $grouped = [];
    foreach ($results as $item) {
        $key = $item['date']->format('Y-m');
        $grouped[$key] = ($grouped[$key] ?? 0) + $item['count'];
    }

    ksort($grouped);
    
    return array_map(function ($month, $count) {
        return ['month' => $month, 'count' => $count];
    }, array_keys($grouped), $grouped);
}

    public function getParticipantCountBySport(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.typesport', 'SUM(m.nb_personne) as totalParticipants')
            ->groupBy('m.typesport')
            ->orderBy('totalParticipants', 'DESC')
            ->getQuery()
            ->getResult();
    }
}