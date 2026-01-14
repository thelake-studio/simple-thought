<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Emotion;
use App\Entity\Activity;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Creamos una instancia de la entidad User
        $user = new User();
        $user->setEmail('alumno@daw.com');
        $user->setNickname('ProgramadorDAW');
        $user->setCreatedAt(new \DateTimeImmutable()); // Fecha de creación actual

        // Ciframos la contraseña '123456'
        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);

        // Le decimos a Doctrine que "prepare" este objeto para guardarlo
        $manager->persist($user);

        // Guardamos los cambios físicamente en la base de datos
        $manager->flush();
    }
}
