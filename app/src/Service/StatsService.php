<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\EntryRepository;
use App\Repository\GoalLogRepository;

/**
 * Servicio encargado de generar las estadísticas y métricas cruzadas de la aplicación.
 * Procesa datos de entradas del diario, estados de ánimo, actividades y objetivos
 * para estructurarlos y alimentar las visualizaciones interactivas (Chart.js).
 */
final class StatsService
{
    /**
     * @param EntryRepository $entryRepository Repositorio de entradas del diario.
     * @param GoalLogRepository $goalLogRepository Repositorio de registros de objetivos.
     */
    public function __construct(
        private readonly EntryRepository $entryRepository,
        private readonly GoalLogRepository $goalLogRepository
    ) {
    }

    /**
     * Calcula la evolución media diaria del estado de ánimo en un rango de fechas.
     *
     * @param User $user El usuario autenticado.
     * @param \DateTimeInterface $startDate Fecha de inicio del análisis.
     * @param \DateTimeInterface $endDate Fecha de fin del análisis.
     * @return array<string, mixed> Datos estructurados para el gráfico de líneas.
     */
    public function getMoodEvolutionData(User $user, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $entries = $this->entryRepository->findEntriesBetweenDates($user, $startDate, $endDate);

        // Convertimos a DateTimeImmutable de forma segura para usar modify() sin romper la interfaz
        $endPeriodDate = \DateTimeImmutable::createFromInterface($endDate)->modify('+1 day');

        $period = new \DatePeriod(
            $startDate,
            new \DateInterval('P1D'),
            $endPeriodDate
        );

        $dailyData = [];
        foreach ($period as $date) {
            $dailyData[$date->format('d/m')] = [];
        }

        foreach ($entries as $entry) {
            if ($entry->getMoodValueSnapshot() !== null) {
                $dateKey = $entry->getDate()->format('d/m');

                if (isset($dailyData[$dateKey])) {
                    $dailyData[$dateKey][] = $entry->getMoodValueSnapshot();
                }
            }
        }

        $labels = [];
        $dataPoints = [];
        foreach ($dailyData as $day => $values) {
            $labels[] = $day;
            $dataPoints[] = count($values) > 0 ? array_sum($values) / count($values) : null;
        }

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
     * Obtiene las actividades más frecuentes registradas en un periodo específico.
     *
     * @param User $user El usuario autenticado.
     * @param \DateTimeInterface $startDate Fecha de inicio del análisis.
     * @param \DateTimeInterface $endDate Fecha de fin del análisis.
     * @return array<string, mixed> Top 5 de actividades para un gráfico de pastel/anillo.
     */
    public function getTopActivitiesData(User $user, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
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
     * Genera una matriz de impacto calculando la nota media de ánimo asociada a cada actividad.
     *
     * @param User $user El usuario autenticado.
     * @param \DateTimeInterface $startDate Fecha de inicio del análisis.
     * @param \DateTimeInterface $endDate Fecha de fin del análisis.
     * @return array<string, mixed> Datos de correlación entre actividades y estado de ánimo.
     */
    public function getActivityMoodMatrixData(User $user, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
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
     * Calcula la correlación entre el cumplimiento de objetivos y el estado de ánimo promedio.
     * Compara los días con avance registrado frente a los días sin avance.
     *
     * @param User $user El usuario autenticado.
     * @param \DateTimeInterface $startDate Fecha de inicio del análisis.
     * @param \DateTimeInterface $endDate Fecha de fin del análisis.
     * @return array<string, mixed> Datos comparativos para Chart.js.
     */
    public function getGoalMoodCorrelationData(User $user, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $entries = $this->entryRepository->findEntriesBetweenDates($user, $startDate, $endDate);
        $goalLogs = $this->goalLogRepository->findLogsBetweenDates($user, $startDate, $endDate);

        $daysWithGoals = [];
        foreach ($goalLogs as $log) {
            $daysWithGoals[$log->getDate()->format('Y-m-d')] = true;
        }

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

        $avgWithGoals = count($moodsWithGoals) > 0 ? array_sum($moodsWithGoals) / count($moodsWithGoals) : 0;
        $avgWithoutGoals = count($moodsWithoutGoals) > 0 ? array_sum($moodsWithoutGoals) / count($moodsWithoutGoals) : 0;

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

    /**
     * Extrae las etiquetas (tags) más frecuentemente asociadas a días excepcionalmente buenos (>= 8) o malos (<= 4).
     *
     * @param User $user El usuario autenticado.
     * @param \DateTimeInterface $startDate Fecha de inicio del análisis.
     * @param \DateTimeInterface $endDate Fecha de fin del análisis.
     * @return array<string, array<string, mixed>> Top 3 de etiquetas positivas y negativas con sus recuentos.
     */
    public function getTagContextData(User $user, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $entries = $this->entryRepository->findEntriesBetweenDates($user, $startDate, $endDate);

        $positiveTags = [];
        $negativeTags = [];

        foreach ($entries as $entry) {
            $mood = $entry->getMoodValueSnapshot();

            if ($mood === null) {
                continue;
            }

            $tags = $entry->getTags();

            if ($mood >= 8) {
                foreach ($tags as $tag) {
                    $name = $tag->getName();
                    if (!isset($positiveTags[$name])) {
                        $positiveTags[$name] = ['count' => 0, 'color' => $tag->getColor() ?? '#198754'];
                    }
                    $positiveTags[$name]['count']++;
                }
            } elseif ($mood <= 4) {
                foreach ($tags as $tag) {
                    $name = $tag->getName();
                    if (!isset($negativeTags[$name])) {
                        $negativeTags[$name] = ['count' => 0, 'color' => $tag->getColor() ?? '#dc3545'];
                    }
                    $negativeTags[$name]['count']++;
                }
            }
        }

        uasort($positiveTags, fn($a, $b) => $b['count'] <=> $a['count']);
        uasort($negativeTags, fn($a, $b) => $b['count'] <=> $a['count']);

        return [
            'positive' => array_slice($positiveTags, 0, 3, true),
            'negative' => array_slice($negativeTags, 0, 3, true),
        ];
    }

    /**
     * Genera la estructura de datos para la visualización "El Año en Píxeles".
     * Organiza la nota media de ánimo en una matriz de meses (1-12) y días (1-31).
     *
     * @param User $user El usuario autenticado.
     * @param \DateTimeInterface $referenceDate Fecha de referencia para extraer el año a consultar.
     * @return array<string, mixed> Matriz del calendario anual con las notas medias diarias.
     */
    public function getYearInPixelsData(User $user, \DateTimeInterface $referenceDate): array
    {
        $year = $referenceDate->format('Y');

        $startDate = new \DateTime("$year-01-01 00:00:00");
        $endDate = new \DateTime("$year-12-31 23:59:59");

        $entries = $this->entryRepository->findEntriesBetweenDates($user, $startDate, $endDate);

        $calendar = [];
        for ($m = 1; $m <= 12; $m++) {
            $dateForMonth = new \DateTime(sprintf('%04d-%02d-01', $year, $m));
            $daysInMonth = (int) $dateForMonth->format('t');

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $calendar[$m][$d] = null;
            }
        }

        $dailyMoods = [];
        foreach ($entries as $entry) {
            if ($entry->getMoodValueSnapshot() !== null) {
                $m = (int) $entry->getDate()->format('n');
                $d = (int) $entry->getDate()->format('j');

                if (!isset($dailyMoods[$m][$d])) {
                    $dailyMoods[$m][$d] = [];
                }
                $dailyMoods[$m][$d][] = $entry->getMoodValueSnapshot();
            }
        }

        foreach ($dailyMoods as $m => $days) {
            foreach ($days as $d => $moods) {
                $calendar[$m][$d] = (int) round(array_sum($moods) / count($moods));
            }
        }

        return [
            'year' => $year,
            'calendar' => $calendar
        ];
    }
}
