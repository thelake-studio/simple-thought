<?php

namespace App\Repository;

use App\Entity\Entry;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repositorio encargado de gestionar las operaciones de base de datos para la entidad Entry.
 * Proporciona métodos para guardar, eliminar y consultar las entradas del diario de los usuarios,
 * incluyendo filtros avanzados por rango de fechas para las analíticas.
 *
 * @extends ServiceEntityRepository<Entry>
 */
class EntryRepository extends ServiceEntityRepository
{
    /**
     * Inicializa el repositorio y lo vincula con la entidad Entry.
     *
     * @param ManagerRegistry $registry Registro del gestor de entidades de Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entry::class);
    }

    /**
     * Persiste una entrada del diario en la base de datos.
     *
     * @param Entry $entity La entrada a guardar.
     * @param bool $flush Si es true, ejecuta las consultas pendientes inmediatamente.
     * @return void
     */
    public function save(Entry $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Elimina una entrada del diario de la base de datos.
     *
     * @param Entry $entity La entrada a eliminar.
     * @param bool $flush Si es true, ejecuta las consultas pendientes inmediatamente.
     * @return void
     */
    public function remove(Entry $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Busca y devuelve todas las entradas del diario de un usuario específico,
     * ordenadas cronológicamente de forma descendente (las más recientes primero).
     *
     * @param User $user El usuario propietario de las entradas.
     * @return array<int, Entry> Lista de entradas del diario.
     */
    public function findAllByUser(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.user = :val')
            ->setParameter('val', $user)
            ->orderBy('e.date', 'DESC')
            ->addOrderBy('e.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Busca y devuelve las entradas del diario de un usuario dentro de un rango de fechas específico.
     * Los resultados se ordenan cronológicamente de forma ascendente (las más antiguas primero),
     * lo cual es ideal para la generación de gráficas evolutivas.
     *
     * @param User $user El usuario propietario de las entradas.
     * @param \DateTimeInterface $startDate Fecha de inicio del filtro.
     * @param \DateTimeInterface $endDate Fecha de fin del filtro.
     * @return array<int, Entry> Lista de entradas dentro del rango de fechas.
     */
    public function findEntriesBetweenDates(User $user, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.user = :user')
            ->andWhere('e.date >= :start')
            ->andWhere('e.date <= :end')
            ->setParameter('user', $user)
            ->setParameter('start', $startDate->format('Y-m-d'))
            ->setParameter('end', $endDate->format('Y-m-d'))
            ->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
