<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repositorio encargado de gestionar las operaciones de base de datos para la entidad Tag.
 * Proporciona métodos para guardar, eliminar y listar las etiquetas personalizadas de los usuarios.
 *
 * @extends ServiceEntityRepository<Tag>
 */
class TagRepository extends ServiceEntityRepository
{
    /**
     * Inicializa el repositorio y lo vincula con la entidad Tag.
     *
     * @param ManagerRegistry $registry Registro del gestor de entidades de Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * Persiste una etiqueta en la base de datos.
     *
     * @param Tag $entity La etiqueta a guardar.
     * @param bool $flush Si es true, ejecuta las consultas pendientes inmediatamente.
     * @return void
     */
    public function save(Tag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Elimina una etiqueta de la base de datos.
     *
     * @param Tag $entity La etiqueta a eliminar.
     * @param bool $flush Si es true, ejecuta las consultas pendientes inmediatamente.
     * @return void
     */
    public function remove(Tag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Busca y devuelve todas las etiquetas pertenecientes a un usuario específico,
     * ordenadas alfabéticamente por su nombre.
     *
     * @param User $user El usuario propietario de las etiquetas.
     * @return array<int, Tag> Lista de etiquetas del usuario.
     */
    public function findAllByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :val')
            ->setParameter('val', $user)
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
