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
     * Calcula los datos de la gráfica de evolución semanal.
     */
    public function getWeeklyMoodData(User $user): array
    {
        $monday = new \DateTime('monday this week');
        $sunday = new \DateTime('sunday this week');
        $entries = $this->entryRepository->findEntriesBetweenDates($user, $monday, $sunday);

        $weeklyData = [
            'Lunes' => [], 'Martes' => [], 'Miércoles' => [],
            'Jueves' => [], 'Viernes' => [], 'Sábado' => [], 'Domingo' => []
        ];

        $diasSemana = [
            'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado', 'Sunday' => 'Domingo'
        ];

        foreach ($entries as $entry) {
            $dayNameEnglish = $entry->getDate()->format('l');
            $dayNameSpanish = $diasSemana[$dayNameEnglish];

            if ($entry->getMoodValueSnapshot() !== null) {
                $weeklyData[$dayNameSpanish][] = $entry->getMoodValueSnapshot();
            }
        }

        $chartDataPoints = [];
        foreach ($weeklyData as $values) {
            $chartDataPoints[] = count($values) > 0 ? array_sum($values) / count($values) : null;
        }

        return [
            'labels' => array_keys($weeklyData),
            'datasets' => [
                [
                    'label' => 'Nivel de Ánimo',
                    'data' => $chartDataPoints,
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
     * Calcula los datos de la gráfica de evolución mensual.
     */
    public function getMonthlyMoodData(User $user): array
    {
        $firstDayOfMonth = new \DateTime('first day of this month');
        $lastDayOfMonth = new \DateTime('last day of this month');
        $monthlyEntries = $this->entryRepository->findEntriesBetweenDates($user, $firstDayOfMonth, $lastDayOfMonth);

        $daysInMonth = (int) $lastDayOfMonth->format('t');

        // Inicializamos el array del mes (del 1 al 28/30/31)
        $monthlyData = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $monthlyData[$i] = [];
        }

        foreach ($monthlyEntries as $entry) {
            $dayOfMonth = (int) $entry->getDate()->format('j');
            if ($entry->getMoodValueSnapshot() !== null) {
                $monthlyData[$dayOfMonth][] = $entry->getMoodValueSnapshot();
            }
        }

        $chartDataPoints = [];
        $labels = [];
        foreach ($monthlyData as $day => $values) {
            $labels[] = $day;
            $chartDataPoints[] = count($values) > 0 ? array_sum($values) / count($values) : null;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Nivel de Ánimo',
                    'data' => $chartDataPoints,
                    'borderColor' => '#198754',
                    'backgroundColor' => 'rgba(25, 135, 84, 0.2)',
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
}
