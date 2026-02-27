<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repositorio encargado de gestionar las operaciones de base de datos para la entidad Activity.
 * Proporciona métodos para guardar, eliminar y consultar las actividades personalizadas de los usuarios.
 *
 * @extends ServiceEntityRepository<Activity>
 */
class ActivityRepository extends ServiceEntityRepository
{
    /**
     * Inicializa el repositorio y lo vincula con la entidad Activity.
     *
     * @param ManagerRegistry $registry Registro del gestor de entidades de Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    /**
     * Persiste una actividad en la base de datos.
     *
     * @param Activity $entity La actividad a guardar.
     * @param bool $flush Si es true, ejecuta las consultas pendientes inmediatamente.
     * @return void
     */
    public function save(Activity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Elimina una actividad de la base de datos.
     *
     * @param Activity $entity La actividad a eliminar.
     * @param bool $flush Si es true, ejecuta las consultas pendientes inmediatamente.
     * @return void
     */
    public function remove(Activity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Busca y devuelve todas las actividades pertenecientes a un usuario específico,
     * ordenadas alfabéticamente por su nombre.
     *
     * @param User $user El usuario propietario de las actividades.
     * @return array<int, Activity> Lista de actividades del usuario.
     */
    public function findAllByUser(User $user): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :val')
            ->setParameter('val', $user)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
