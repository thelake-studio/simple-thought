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

        // 2. Crear un catálogo de Emociones para este usuario
        $emocionesDatos = [
            ['Alegría', 9, '#FFD700', 'fa-smile'],
            ['Calma', 7, '#ADD8E6', 'fa-leaf'],
            ['Cansancio', 4, '#808080', 'fa-battery-quarter'],
            ['Tristeza', 2, '#4682B4', 'fa-frown'],
        ];

        foreach ($emocionesDatos as [$nombre, $valor, $color, $icono]) {
            $emotion = new Emotion();
            $emotion->setName($nombre);
            $emotion->setValue($valor); // Este valor (1-10) es para tus futuras gráficas
            $emotion->setColor($color);
            $emotion->setIcon($icono);

            // ¡IMPORTANTE!: Vinculamos la emoción al usuario que creamos arriba
            $emotion->setUser($user); //

            $manager->persist($emotion);
        }

        // 3. Crear un catálogo de Actividades para este usuario
        $actividadesDatos = [
            ['Deporte', 'Salud', 'fa-running', '#FF4500'],
            ['Programar', 'Estudios', 'fa-code', '#1E90FF'],
            ['Lectura', 'Ocio', 'fa-book', '#8A2BE2'],
            ['Meditar', 'Salud', 'fa-spa', '#20B2AA'],
        ];

        foreach ($actividadesDatos as [$nombre, $categoria, $icono, $color]) {
            $activity = new Activity();
            $activity->setName($nombre);
            $activity->setCategory($categoria);
            $activity->setIcon($icono);
            $activity->setColor($color);

            // Vinculamos la actividad al usuario
            $activity->setUser($user);

            $manager->persist($activity);
        }

        // Guardamos los cambios físicamente en la base de datos
        $manager->flush();
    }
}
