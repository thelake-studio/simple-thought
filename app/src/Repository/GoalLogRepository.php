<?php

namespace App\Repository;

use App\Entity\GoalLog;
use App\Entity\Goal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GoalLog>
 */
class GoalLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GoalLog::class);
    }

    public function save(GoalLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GoalLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUser($user): array
    {
        return $this->createQueryBuilder('gl')
            ->join('gl.goal', 'g')
            ->andWhere('g.user = :user')
            ->setParameter('user', $user)
            ->orderBy('gl.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getSumBetweenDates(Goal $goal, \DateTimeInterface $start, \DateTimeInterface $end): int
    {
        $result = $this->createQueryBuilder('gl')
            ->select('SUM(gl.value)')
            ->andWhere('gl.goal = :goal')
            ->andWhere('gl.date >= :start')
            ->andWhere('gl.date <= :end')
            ->setParameter('goal', $goal)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();

        // Si no hay resultados, devuelve null, así que lo forzamos a 0
        return (int) $result;
    }
}
