<?php

namespace App\Repository;

use App\Entity\Emotion;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repositorio encargado de gestionar las operaciones de base de datos para la entidad Emotion.
 * Proporciona métodos para guardar, eliminar y consultar las emociones personalizadas de los usuarios.
 *
 * @extends ServiceEntityRepository<Emotion>
 */
class EmotionRepository extends ServiceEntityRepository
{
    /**
     * Inicializa el repositorio y lo vincula con la entidad Emotion.
     *
     * @param ManagerRegistry $registry Registro del gestor de entidades de Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emotion::class);
    }

    /**
     * Persiste una emoción en la base de datos.
     *
     * @param Emotion $entity La emoción a guardar.
     * @param bool $flush Si es true, ejecuta las consultas pendientes inmediatamente.
     * @return void
     */
    public function save(Emotion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Elimina una emoción de la base de datos.
     *
     * @param Emotion $entity La emoción a eliminar.
     * @param bool $flush Si es true, ejecuta las consultas pendientes inmediatamente.
     * @return void
     */
    public function remove(Emotion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Busca y devuelve todas las emociones pertenecientes a un usuario específico,
     * ordenadas alfabéticamente por su nombre.
     *
     * @param User $user El usuario propietario de las emociones.
     * @return array<int, Emotion> Lista de emociones del usuario.
     */
    public function findAllByUser(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.user = :user')
            ->setParameter('user', $user)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
