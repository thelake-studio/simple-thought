<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\EntryRepository;
use App\Repository\GoalLogRepository;

class StatsService
{
    public function __construct(
        private readonly EntryRepository $entryRepository,
        private readonly GoalLogRepository $goalLogRepository
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
     * Top Actividades: Ahora dinámico por fechas
     */
    public function getTopActivitiesData(User $user, \DateTime $startDate, \DateTime $endDate): array
    {
        // 1. Usamos la misma consulta que creamos para la evolución
        $entries = $this->entryRepository->findEntriesBetweenDates($user, $startDate, $endDate);

        $activityCounts = [];

        foreach ($entries as $entry) {
            foreach ($entry->getActivities() as $activity) {
                $name = $activity->getName();
                if (!isset($activityCounts[$name])) {
                    $activityCounts[$name] = 0;
                }
                $activityCounts[$name]++;
            }
        }

        arsort($activityCounts);
        $topActivities = array_slice($activityCounts, 0, 5, true);

        return [
            'labels' => array_keys($topActivities),
            'datasets' => [
                [
                    'data' => array_values($topActivities),
                    'backgroundColor' => ['#f1c40f', '#e67e22', '#e74c3c', '#9b59b6', '#3498db'],
                    'borderWidth' => 0,
                    'hoverOffset' => 4
                ]
            ]
        ];
    }

    /**
     * Matriz de Impacto: Ahora dinámica por fechas
     */
    public function getActivityMoodMatrixData(User $user, \DateTime $startDate, \DateTime $endDate): array
    {
        // 1. Filtramos por fechas
        $entries = $this->entryRepository->findEntriesBetweenDates($user, $startDate, $endDate);

        $activityMoods = [];

        foreach ($entries as $entry) {
            if ($entry->getMoodValueSnapshot() !== null) {
                foreach ($entry->getActivities() as $activity) {
                    $name = $activity->getName();
                    if (!isset($activityMoods[$name])) {
                        $activityMoods[$name] = [];
                    }
                    $activityMoods[$name][] = $entry->getMoodValueSnapshot();
                }
            }
        }

        $matrixData = [];
        foreach ($activityMoods as $name => $moods) {
            $matrixData[$name] = count($moods) > 0 ? array_sum($moods) / count($moods) : 0;
        }

        arsort($matrixData);
        $topMatrix = array_slice($matrixData, 0, 7, true);

        return [
            'labels' => array_keys($topMatrix),
            'datasets' => [
                [
                    'label' => 'Nota Media del Ánimo',
                    'data' => array_values($topMatrix),
                    'backgroundColor' => 'rgba(155, 89, 182, 0.7)',
                    'borderColor' => '#8e44ad',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * Métrica Cruzada: Ánimo en días con objetivos vs. días sin objetivos
     */
    public function getGoalMoodCorrelationData(User $user, \DateTime $startDate, \DateTime $endDate): array
    {
        // 1. Extraemos las entradas y los progresos de ese periodo
        $entries = $this->entryRepository->findEntriesBetweenDates($user, $startDate, $endDate);
        $goalLogs = $this->goalLogRepository->findLogsBetweenDates($user, $startDate, $endDate);

        // 2. Creamos un "calendario" rápido para saber qué días hubo avance
        $daysWithGoals = [];
        foreach ($goalLogs as $log) {
            $daysWithGoals[$log->getDate()->format('Y-m-d')] = true;
        }

        // 3. Separamos las notas de ánimo en dos cajas
        $moodsWithGoals = [];
        $moodsWithoutGoals = [];

        foreach ($entries as $entry) {
            if ($entry->getMoodValueSnapshot() !== null) {
                $dateString = $entry->getDate()->format('Y-m-d');

                if (isset($daysWithGoals[$dateString])) {
                    $moodsWithGoals[] = $entry->getMoodValueSnapshot();
                } else {
                    $moodsWithoutGoals[] = $entry->getMoodValueSnapshot();
                }
            }
        }

        // 4. Calculamos las medias (evitando división por cero si no hay datos)
        $avgWithGoals = count($moodsWithGoals) > 0 ? array_sum($moodsWithGoals) / count($moodsWithGoals) : 0;
        $avgWithoutGoals = count($moodsWithoutGoals) > 0 ? array_sum($moodsWithoutGoals) / count($moodsWithoutGoals) : 0;

        // 5. Preparamos el formato para Chart.js
        return [
            'labels' => ['Avanzando en objetivos', 'Sin avances registrados'],
            'datasets' => [
                [
                    'label' => 'Nota Media del Ánimo',
                    'data' => [round($avgWithGoals, 2), round($avgWithoutGoals, 2)],
                    'backgroundColor' => [
                        'rgba(46, 204, 113, 0.7)',
                        'rgba(231, 76, 60, 0.7)'
                    ],
                    'borderColor' => ['#27ae60', '#c0392b'],
                    'borderWidth' => 1
                ]
            ]
        ];
    }
}
