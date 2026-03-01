<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * Repositorio encargado de gestionar las operaciones de base de datos para la entidad User.
 * Gestiona el almacenamiento, recuperación y la actualización automática de hashes de contraseñas.
 *
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * Inicializa el repositorio y lo vincula con la entidad User.
     *
     * @param ManagerRegistry $registry Registro del gestor de entidades de Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Actualiza automáticamente (re-hashea) la contraseña del usuario a lo largo del tiempo
     * cuando cambian los algoritmos de seguridad o los costes de cifrado soportados por Symfony.
     *
     * @param PasswordAuthenticatedUserInterface $user El usuario autenticado al que se le actualizará la contraseña.
     * @param string $newHashedPassword La nueva contraseña ya cifrada (hash).
     * @return void
     * @throws UnsupportedUserException Si la instancia de usuario proporcionada no es soportada.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Las instancias de "%s" no están soportadas.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
