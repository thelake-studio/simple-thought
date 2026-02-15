<?php

namespace App\Service;

use App\Entity\Goal;
use App\Repository\GoalLogRepository;

class GoalService
{
    private GoalLogRepository $goalLogRepository;

    public function __construct(GoalLogRepository $goalLogRepository)
    {
        $this->goalLogRepository = $goalLogRepository;
    }

    public function getProgress(Goal $goal): array
    {
        if ($goal->getType() !== Goal::TYPE_SUM) {
            return [];
        }

        // 2. Calculamos la ventana de tiempo según la periodicidad
        $today = new \DateTime('today');
        $start = clone $today;
        $end = clone $today;

        switch ($goal->getPeriod()) {
            case Goal::PERIOD_WEEKLY:
                // Lunes de esta semana a Domingo de esta semana
                $start->modify('monday this week');
                $end->modify('sunday this week');
                break;

            case Goal::PERIOD_MONTHLY:
                // Día 1 de este mes a Último día de este mes
                $start->modify('first day of this month');
                $end->modify('last day of this month');
                break;

            case Goal::PERIOD_DAILY:
                break;
        }

        // 3. Pedimos al repositorio que sume
        $end->setTime(23, 59, 59);

        $currentValue = $this->goalLogRepository->getSumBetweenDates($goal, $start, $end);

        // 4. Calculamos porcentaje
        $target = $goal->getTargetValue() ?? 1; // Si es nulo, ponemos 1 para no romper
        $percentage = ($target > 0) ? round(($currentValue / $target) * 100) : 0;

        // Limitamos el porcentaje al 100%
        $percentageDisplay = min($percentage, 100);

        return [
            'current' => $currentValue,
            'target' => $target,
            'percentage' => $percentage,
            'period_start' => $start,
            'period_end' => $end
        ];
    }

    public function getStreak(Goal $goal): array
    {
        if ($goal->getType() !== Goal::TYPE_STREAK) {
            return [];
        }

        // 1. Obtenemos el historial ordenado (Hoy, ayer, anteayer...)
        $logs = $this->goalLogRepository->findLogsForGoal($goal);

        if (empty($logs)) {
            return ['current_streak' => 0, 'is_completed_today' => false];
        }

        // 2. Preparamos variables de control
        $streak = 0;
        $today = new \DateTime('today');
        $yesterday = new \DateTime('yesterday');

        // Verificamos si el último log es de hoy
        $lastLogDate = $logs[0]->getDate()->format('Y-m-d');
        $todayStr = $today->format('Y-m-d');
        $isCompletedToday = ($lastLogDate === $todayStr);

        // 3. Algoritmo de "Cadena Continua"
        // Empezamos a contar desde "la fecha esperada".
        // Si ya hicimos hoy, esperamos encontrar ayer. Si no hicimos hoy, esperamos encontrar ayer.

        // Truco: Para calcular la racha, iteramos los logs.
        // Usamos una fecha "puntero" que debe coincidir con el log.

        $checkDate = $isCompletedToday ? $today : $yesterday;

        // Comprobación rápida: Si no completaste hoy, y el último log NO es de ayer... racha rota.
        if (!$isCompletedToday && $lastLogDate !== $yesterday->format('Y-m-d')) {
             return ['current_streak' => 0, 'is_completed_today' => false];
        }

        // Recorremos los logs para contar la racha
        // Nota: Agrupamos por día para evitar que 2 logs el mismo día sumen 2 de racha.
        $processedDays = [];

        foreach ($logs as $log) {
            $logDateStr = $log->getDate()->format('Y-m-d');

            // Si ya contamos este día (ej: 2 logs el mismo día), lo saltamos
            if (in_array($logDateStr, $processedDays)) {
                continue;
            }

            // Si el log coincide con la fecha que buscamos, sumamos racha
            if ($logDateStr === $checkDate->format('Y-m-d')) {
                $streak++;
                $processedDays[] = $logDateStr;
                // Movemos el puntero un día hacia atrás
                $checkDate->modify('-1 day');
            } else {
                // ¡Hueco encontrado! Se acabó la racha.
                break;
            }
        }

        return [
            'current_streak' => $streak,
            'is_completed_today' => $isCompletedToday,
        ];
    }
}
