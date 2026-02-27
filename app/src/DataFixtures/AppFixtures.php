<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Emotion;
use App\Entity\Activity;
use App\Entity\Tag;
use App\Entity\Entry;
use App\Entity\Goal;
use App\Entity\GoalLog;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Clase encargada de cargar datos de prueba (Fixtures) en la base de datos.
 * Genera un usuario base con su catálogo completo de emociones, actividades,
 * etiquetas, entradas de diario y diferentes escenarios de objetivos para entorno de desarrollo.
 */
final class AppFixtures extends Fixture
{
    /**
     * Constructor para la inyección de dependencias.
     *
     * @param UserPasswordHasherInterface $userPasswordHasher Servicio para el hash de contraseñas.
     */
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    /**
     * Ejecuta la generación y persistencia de los datos ficticios.
     *
     * @param ObjectManager $manager Gestor de entidades de Doctrine.
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('alumno@daw.com');
        $user->setNickname('ProgramadorDAW');
        $user->setCreatedAt(new \DateTimeImmutable());

        $hashedPassword = $this->userPasswordHasher->hashPassword($user, '123456');
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        $emotionsData = [
            ['Alegría', 9, '#FFD700', 'fa-smile'],
            ['Calma', 7, '#ADD8E6', 'fa-leaf'],
            ['Cansancio', 4, '#808080', 'fa-battery-quarter'],
            ['Tristeza', 2, '#4682B4', 'fa-frown'],
        ];

        $lastEmotion = null;
        foreach ($emotionsData as [$name, $value, $color, $icon]) {
            $emotion = new Emotion();
            $emotion->setName($name);
            $emotion->setValue($value);
            $emotion->setColor($color);
            $emotion->setIcon($icon);
            $emotion->setUser($user);

            $manager->persist($emotion);
            $lastEmotion = $emotion;
        }

        $activitiesData = [
            ['Deporte', 'Salud', 'fa-running', '#FF4500'],
            ['Programar', 'Estudios', 'fa-code', '#1E90FF'],
            ['Lectura', 'Ocio', 'fa-book', '#8A2BE2'],
            ['Meditar', 'Salud', 'fa-spa', '#20B2AA'],
        ];

        $lastActivity = null;
        foreach ($activitiesData as [$name, $category, $icon, $color]) {
            $activity = new Activity();
            $activity->setName($name);
            $activity->setCategory($category);
            $activity->setIcon($icon);
            $activity->setColor($color);
            $activity->setUser($user);

            $manager->persist($activity);
            $lastActivity = $activity;
        }

        $tagsData = [
            ['Importante', '#FF0000'],
            ['Reflexión', '#4B0082'],
            ['Logro', '#32CD32'],
            ['Idea', '#FFFF00'],
        ];

        $lastTag = null;
        foreach ($tagsData as [$name, $color]) {
            $tag = new Tag();
            $tag->setName($name);
            $tag->setColor($color);
            $tag->setUser($user);

            $manager->persist($tag);
            $lastTag = $tag;
        }

        $entry = new Entry();
        $entry->setTitle('Mi primer día programando Simple Thought');
        $entry->setContent('Hoy he avanzado muchísimo en el TFG. He configurado las validaciones y las fixtures.');
        $entry->setDate(new \DateTime());

        if ($lastEmotion !== null) {
            $entry->setMoodValueSnapshot($lastEmotion->getValue());
            $entry->setEmotion($lastEmotion);
        }

        $entry->setUser($user);

        if ($lastActivity !== null) {
            $entry->addActivity($lastActivity);
        }

        if ($lastTag !== null) {
            $entry->addTag($lastTag);
        }

        $entry->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($entry);

        // Escenario 1: Objetivo de racha activa (5 días seguidos)
        $goalReading = new Goal();
        $goalReading->setName('Leer 30 minutos');
        $goalReading->setType(Goal::TYPE_STREAK);
        $goalReading->setPeriod(Goal::PERIOD_DAILY);
        $goalReading->setTargetValue(1);
        $goalReading->setUser($user);
        $goalReading->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($goalReading);

        for ($i = 0; $i < 5; $i++) {
            $log = new GoalLog();
            $log->setGoal($goalReading);
            $log->setDate(new \DateTimeImmutable("- $i days"));
            $log->setValue(1);
            $manager->persist($log);
        }

        // Escenario 2: Objetivo de racha rota (Fallo en el día de ayer)
        $goalSugar = new Goal();
        $goalSugar->setName('Días sin azúcar');
        $goalSugar->setType(Goal::TYPE_STREAK);
        $goalSugar->setPeriod(Goal::PERIOD_DAILY);
        $goalSugar->setUser($user);
        $goalSugar->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($goalSugar);

        for ($i = 3; $i <= 6; $i++) {
            $log = new GoalLog();
            $log->setGoal($goalSugar);
            $log->setDate(new \DateTimeImmutable("- $i days"));
            $log->setValue(1);
            $manager->persist($log);
        }

        // Escenario 3: Objetivo de suma (Progreso acumulativo parcial)
        $goalSteps = new Goal();
        $goalSteps->setName('Caminar 20k pasos');
        $goalSteps->setType(Goal::TYPE_SUM);
        $goalSteps->setPeriod(Goal::PERIOD_WEEKLY);
        $goalSteps->setTargetValue(20000);
        $goalSteps->setUser($user);
        $goalSteps->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($goalSteps);

        $logStepsToday = new GoalLog();
        $logStepsToday->setGoal($goalSteps);
        $logStepsToday->setDate(new \DateTimeImmutable('today'));
        $logStepsToday->setValue(5000);
        $manager->persist($logStepsToday);

        $logStepsYesterday = new GoalLog();
        $logStepsYesterday->setGoal($goalSteps);
        $logStepsYesterday->setDate(new \DateTimeImmutable('-1 day'));
        $logStepsYesterday->setValue(7500);
        $manager->persist($logStepsYesterday);

        $manager->flush();
    }
}
