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
}
