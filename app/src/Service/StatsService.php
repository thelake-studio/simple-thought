<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\EntryRepository;

class StatsService
{
    public function __construct(
        private readonly EntryRepository $entryRepository
    ) {
    }

    /**
     * Evolución del Ánimo Dinámica: Calcula la media de ánimo en un rango de fechas.
     */
    public function getMoodEvolutionData(User $user, \DateTime $startDate, \DateTime $endDate): array
    {
        // 1. Buscamos las entradas en ese rango específico
        $entries = $this->entryRepository->findEntriesBetweenDates($user, $startDate, $endDate);

        // 2. Generamos un array con todos los días del rango
        $period = new \DatePeriod(
            $startDate,
            new \DateInterval('P1D'),
            (clone $endDate)->modify('+1 day')
        );

        $dailyData = [];
        foreach ($period as $date) {
            $dailyData[$date->format('d/m')] = [];
        }

        // 3. Rellenamos el array con las notas de ánimo de las entradas reales
        foreach ($entries as $entry) {
            if ($entry->getMoodValueSnapshot() !== null) {
                $dateKey = $entry->getDate()->format('d/m');

                if (isset($dailyData[$dateKey])) {
                    $dailyData[$dateKey][] = $entry->getMoodValueSnapshot();
                }
            }
        }

        // 4. Calculamos la media de cada día
        $labels = [];
        $dataPoints = [];
        foreach ($dailyData as $day => $values) {
            $labels[] = $day;
            $dataPoints[] = count($values) > 0 ? array_sum($values) / count($values) : null;
        }

        // 5. Devolvemos el formato de Chart.js
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Evolución del Ánimo',
                    'data' => $dataPoints,
                    'borderColor' => '#0d6efd',
                    'backgroundColor' => 'rgba(13, 110, 253, 0.2)',
                    'tension' => 0.4,
                    'fill' => true,
                    'spanGaps' => true
                ]
            ]
        ];
    }

    /**
     * Calcula el Top 5 de actividades más realizadas.
     */
    public function getTopActivitiesData(User $user): array
    {
        $entries = $this->entryRepository->findAllByUser($user);
        $activityCounts = [];

        // 1. Contamos las actividades de cada entrada
        foreach ($entries as $entry) {
            foreach ($entry->getActivities() as $activity) {
                $name = $activity->getName();
                if (!isset($activityCounts[$name])) {
                    $activityCounts[$name] = 0;
                }
                $activityCounts[$name]++;
            }
        }

        // 2. Ordenamos de mayor a menor y cogemos las 5 primeras
        arsort($activityCounts);
        $topActivities = array_slice($activityCounts, 0, 5, true);

        // 3. Paleta de colores chula para el Donut
        $backgroundColors = [
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 99, 132, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
        ];

        // 4. Formato Chart.js
        return [
            'labels' => array_keys($topActivities),
            'datasets' => [
                [
                    'label' => 'Veces realizada',
                    'data' => array_values($topActivities),
                    'backgroundColor' => array_slice($backgroundColors, 0, count($topActivities)),
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff'
                ]
            ]
        ];
    }

    /**
     * Matriz Actividad-Emoción: Calcula la media de ánimo por cada actividad.
     */
    public function getActivityMoodMatrixData(User $user): array
    {
        $entries = $this->entryRepository->findAllByUser($user);
        $activityMoods = [];

        // 1. Recopilamos todos los valores de ánimo asociados a cada actividad
        foreach ($entries as $entry) {
            $moodValue = $entry->getMoodValueSnapshot();

            // Solo contamos si la entrada tiene un estado de ánimo registrado
            if ($moodValue !== null) {
                foreach ($entry->getActivities() as $activity) {
                    $name = $activity->getName();
                    if (!isset($activityMoods[$name])) {
                        $activityMoods[$name] = [];
                    }
                    $activityMoods[$name][] = $moodValue;
                }
            }
        }

        // 2. Calculamos la media matemática para cada actividad
        $activityAverages = [];
        foreach ($activityMoods as $name => $moods) {
            $activityAverages[$name] = array_sum($moods) / count($moods);
        }

        // 3. Ordenamos de mayor a menor para ver qué nos hace más felices
        arsort($activityAverages);

        // Nos quedamos con el Top 7 para que la gráfica se vea limpia
        $topActivities = array_slice($activityAverages, 0, 7, true);

        // 4. Formato Chart.js
        return [
            'labels' => array_keys($topActivities),
            'datasets' => [
                [
                    'label' => 'Media de Ánimo (Sobre 10)',
                    'data' => array_values($topActivities),
                    'backgroundColor' => 'rgba(153, 102, 255, 0.6)',
                    'borderColor' => '#9966ff',
                    'borderWidth' => 1
                ]
            ]
        ];
    }
}
