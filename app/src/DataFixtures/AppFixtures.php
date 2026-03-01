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

final class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // --- 1. CREACIÓN DEL USUARIO ---
        $user = new User();
        $user->setEmail('alumno@daw.com');
        $user->setNickname('José Luis');
        $user->setCreatedAt(new \DateTimeImmutable('-3 months'));

        $hashedPassword = $this->userPasswordHasher->hashPassword($user, '123456');
        $user->setPassword($hashedPassword);
        $manager->persist($user);

        // --- 2. CATÁLOGO DE EMOCIONES ---
        $emotionsData = [
            ['Euforia', 10, '#198754', 'fa-solid fa-face-grin-stars'],
            ['Alegría', 8, '#20c997', 'fa-solid fa-face-smile'],
            ['Calma', 6, '#0dcaf0', 'fa-solid fa-face-smile-beam'],
            ['Cansancio', 4, '#ffc107', 'fa-solid fa-face-meh'],
            ['Ansiedad', 3, '#fd7e14', 'fa-solid fa-face-grimace'],
            ['Tristeza', 2, '#dc3545', 'fa-solid fa-face-frown'],
        ];

        $emotions = [];
        foreach ($emotionsData as [$name, $value, $color, $icon]) {
            $emotion = new Emotion();
            $emotion->setName($name)->setValue($value)->setColor($color)->setIcon($icon)->setUser($user);
            $manager->persist($emotion);
            $emotions[] = $emotion;
        }

        // --- 3. CATÁLOGO DE ACTIVIDADES ---
        $activitiesData = [
            ['Gimnasio', 'Salud', 'fa-solid fa-dumbbell', '#FF4500'],
            ['Programar TFG', 'Estudios', 'fa-solid fa-laptop-code', '#0d6efd'],
            ['Lectura', 'Ocio', 'fa-solid fa-book-open', '#6f42c1'],
            ['Meditar', 'Salud', 'fa-solid fa-spa', '#20c997'],
            ['Amigos', 'Social', 'fa-solid fa-user-group', '#fd7e14'],
            ['Videojuegos', 'Ocio', 'fa-solid fa-gamepad', '#dc3545'],
        ];

        $activities = [];
        foreach ($activitiesData as [$name, $category, $icon, $color]) {
            $activity = new Activity();
            $activity->setName($name)->setCategory($category)->setIcon($icon)->setColor($color)->setUser($user);
            $manager->persist($activity);
            $activities[] = $activity;
        }

        // --- 4. CATÁLOGO DE ETIQUETAS ---
        $tagsData = [
            ['Productivo', '#198754'],
            ['Relajado', '#0dcaf0'],
            ['Estresante', '#dc3545'],
            ['Inspiración', '#ffc107'],
            ['Familia', '#d63384'],
        ];

        $tags = [];
        foreach ($tagsData as [$name, $color]) {
            $tag = new Tag();
            $tag->setName($name)->setColor($color)->setUser($user);
            $manager->persist($tag);
            $tags[] = $tag;
        }

        // --- 5. HISTORIAL DE DIARIO (60 Días para llenar gráficas) ---
        $entryTitles = [
            'Un día bastante normal', 'Avanzando a tope con el proyecto',
            'Hoy necesitaba un descanso', 'Día increíble con amigos',
            'Mucho estrés acumulado', 'Pequeños logros', 'Reflexiones de domingo'
        ];

        for ($i = 60; $i >= 0; $i--) {
            $date = new \DateTimeImmutable("- $i days");

            // Elegir emoción (sesgada hacia valores positivos)
            $weightedIndices = [0, 0, 1, 1, 1, 2, 2, 3, 4, 5];
            $randomIndex = $weightedIndices[array_rand($weightedIndices)];
            $randomEmotion = $emotions[$randomIndex];

            $entry = new Entry();
            $entry->setTitle($entryTitles[array_rand($entryTitles)]);
            $entry->setContent("Querido diario, hoy es " . $date->format('d/m/Y') . ". " .
                "El día ha estado marcado por la emoción de " . $randomEmotion->getName() . ". " .
                "Sigo trabajando en mantener mis hábitos, aunque a veces cuesta. ¡Mañana más!");
            $entry->setDate(\DateTime::createFromImmutable($date));
            $entry->setMoodValueSnapshot($randomEmotion->getValue());
            $entry->setEmotion($randomEmotion);
            $entry->setUser($user);
            $entry->setCreatedAt($date->setTime(20, rand(0, 59))); // Creado por la noche

            // Añadir de 1 a 3 actividades aleatorias
            $randomActivitiesKeys = (array) array_rand($activities, rand(1, 3));
            foreach ($randomActivitiesKeys as $key) {
                $entry->addActivity($activities[$key]);
            }

            // Añadir de 0 a 2 etiquetas aleatorias
            $numTags = rand(0, 2);
            if ($numTags > 0) {
                $randomTagsKeys = (array) array_rand($tags, $numTags);
                foreach ($randomTagsKeys as $key) {
                    $entry->addTag($tags[$key]);
                }
            }

            $manager->persist($entry);
        }

        // --- 6. OBJETIVOS Y REGISTROS (GOALS) ---

        // Objetivo 1: Racha Activa Perfecta (15 días)
        $goalMeditation = new Goal();
        $goalMeditation->setName('Meditar 10 min');
        $goalMeditation->setType(Goal::TYPE_STREAK);
        $goalMeditation->setPeriod(Goal::PERIOD_DAILY);
        $goalMeditation->setUser($user);
        $goalMeditation->setCreatedAt(new \DateTimeImmutable('-20 days'));
        $manager->persist($goalMeditation);

        for ($i = 0; $i < 15; $i++) {
            $log = new GoalLog();
            $log->setGoal($goalMeditation)->setDate(new \DateTimeImmutable("- $i days"))->setValue(1);
            $manager->persist($log);
        }

        // Objetivo 2: Racha Rota (No hizo ayer)
        $goalJunkFood = new Goal();
        $goalJunkFood->setName('Cero comida basura');
        $goalJunkFood->setType(Goal::TYPE_STREAK);
        $goalJunkFood->setPeriod(Goal::PERIOD_DAILY);
        $goalJunkFood->setUser($user);
        $goalJunkFood->setCreatedAt(new \DateTimeImmutable('-30 days'));
        $manager->persist($goalJunkFood);

        // Registros (hace 5, 4, 3 y 2 días, pero rompió la racha ayer y hoy)
        for ($i = 2; $i <= 5; $i++) {
            $log = new GoalLog();
            $log->setGoal($goalJunkFood)->setDate(new \DateTimeImmutable("- $i days"))->setValue(1);
            $manager->persist($log);
        }

        // Objetivo 3: Acumulativo Semanal (Casi completo)
        $goalSteps = new Goal();
        $goalSteps->setName('Caminar 50k pasos');
        $goalSteps->setType(Goal::TYPE_SUM);
        $goalSteps->setPeriod(Goal::PERIOD_WEEKLY);
        $goalSteps->setTargetValue(50000);
        $goalSteps->setUser($user);
        $goalSteps->setCreatedAt(new \DateTimeImmutable('-10 days'));
        $manager->persist($goalSteps);

        $logSteps1 = new GoalLog();
        $logSteps1->setGoal($goalSteps)->setDate(new \DateTimeImmutable('today'))->setValue(12000);
        $manager->persist($logSteps1);

        $logSteps2 = new GoalLog();
        $logSteps2->setGoal($goalSteps)->setDate(new \DateTimeImmutable('-2 days'))->setValue(15500);
        $manager->persist($logSteps2);

        $logSteps3 = new GoalLog();
        $logSteps3->setGoal($goalSteps)->setDate(new \DateTimeImmutable('-4 days'))->setValue(18000);
        $manager->persist($logSteps3);

        // Objetivo 4: Acumulativo Mensual
        $goalReading = new Goal();
        $goalReading->setName('Leer 500 páginas');
        $goalReading->setType(Goal::TYPE_SUM);
        $goalReading->setPeriod(Goal::PERIOD_MONTHLY);
        $goalReading->setTargetValue(500);
        $goalReading->setUser($user);
        $goalReading->setCreatedAt(new \DateTimeImmutable('first day of this month'));
        $manager->persist($goalReading);

        $logRead1 = new GoalLog();
        $logRead1->setGoal($goalReading)->setDate(new \DateTimeImmutable('-1 day'))->setValue(50);
        $manager->persist($logRead1);

        $logRead2 = new GoalLog();
        $logRead2->setGoal($goalReading)->setDate(new \DateTimeImmutable('-5 days'))->setValue(120);
        $manager->persist($logRead2);

        // Volcar todo a la base de datos
        $manager->flush();
    }
}
