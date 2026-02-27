<?php

namespace App\Repository;

use App\Entity\Goal;
use App\Entity\GoalLog;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repositorio encargado de gestionar las operaciones de base de datos para la entidad GoalLog.
 * Permite registrar, eliminar y consultar los avances numéricos de los objetivos del usuario,
 * incluyendo cálculos sumatorios y filtros por fechas para las estadísticas.
 *
 * @extends ServiceEntityRepository<GoalLog>
 */
class GoalLogRepository extends ServiceEntityRepository
{
    /**
     * Inicializa el repositorio y lo vincula con la entidad GoalLog.
     *
     * @param ManagerRegistry $registry Registro del gestor de entidades de Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GoalLog::class);
    }

    /**
     * Persiste un registro de progreso en la base de datos.
     *
     * @param GoalLog $entity El log a guardar.
     * @param bool $flush Si es true, ejecuta las consultas pendientes inmediatamente.
     * @return void
     */
    public function save(GoalLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Elimina un registro de progreso de la base de datos.
     *
     * @param GoalLog $entity El log a eliminar.
     * @param bool $flush Si es true, ejecuta las consultas pendientes inmediatamente.
     * @return void
     */
    public function remove(GoalLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Busca y devuelve todos los registros de progreso pertenecientes a un usuario,
     * ordenados de forma descendente por fecha.
     *
     * @param User $user El usuario propietario de los registros.
     * @return array<int, GoalLog> Lista de registros del usuario.
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('gl')
            ->join('gl.goal', 'g')
            ->andWhere('g.user = :user')
            ->setParameter('user', $user)
            ->orderBy('gl.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcula y devuelve la suma total de los valores de progreso de un objetivo
     * dentro de un rango de fechas específico.
     *
     * @param Goal $goal El objetivo a evaluar.
     * @param \DateTimeInterface $start Fecha de inicio del filtro.
     * @param \DateTimeInterface $end Fecha de fin del filtro.
     * @return int La suma total del progreso (forzada a 0 si es nula).
     */
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

        return (int) $result;
    }

    /**
     * Busca y devuelve todos los registros de progreso asociados a un objetivo específico.
     *
     * @param Goal $goal El objetivo del que se quieren obtener los registros.
     * @return array<int, GoalLog> Lista de registros del objetivo.
     */
    public function findLogsForGoal(Goal $goal): array
    {
        return $this->createQueryBuilder('gl')
            ->andWhere('gl.goal = :goal')
            ->setParameter('goal', $goal)
            ->orderBy('gl.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Busca y devuelve los registros de progreso de un usuario dentro de un rango de fechas.
     *
     * @param User $user El usuario propietario.
     * @param \DateTimeInterface $startDate Fecha de inicio del filtro.
     * @param \DateTimeInterface $endDate Fecha de fin del filtro.
     * @return array<int, GoalLog> Lista de registros en el rango especificado.
     */
    public function findLogsBetweenDates(User $user, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('gl')
            ->join('gl.goal', 'g')
            ->andWhere('g.user = :user')
            ->andWhere('gl.date >= :start')
            ->andWhere('gl.date <= :end')
            ->setParameter('user', $user)
            ->setParameter('start', $startDate->format('Y-m-d 00:00:00'))
            ->setParameter('end', $endDate->format('Y-m-d 23:59:59'))
            ->getQuery()
            ->getResult();
    }
}
