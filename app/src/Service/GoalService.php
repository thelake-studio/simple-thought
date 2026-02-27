<?php

namespace App\Service;

use App\Entity\Goal;
use App\Repository\GoalLogRepository;

/**
 * Servicio encargado de gestionar la lógica de negocio compleja de los objetivos (Goals).
 * Calcula el progreso acumulado y evalúa las rachas de días consecutivos.
 */
final class GoalService
{
    /**
     * @param GoalLogRepository $goalLogRepository Repositorio de registros de objetivos.
     */
    public function __construct(
        private readonly GoalLogRepository $goalLogRepository
    ) {
    }

    /**
     * Calcula el progreso actual de un objetivo de tipo acumulativo (SUM)
     * basándose en su periodicidad (diaria, semanal o mensual).
     *
     * @param Goal $goal El objetivo a evaluar.
     * @return array<string, mixed> Datos del progreso (actual, objetivo, porcentaje y fechas del periodo).
     */
    public function getProgress(Goal $goal): array
    {
        if ($goal->getType() !== Goal::TYPE_SUM) {
            return [];
        }

        $today = new \DateTime('today');
        $start = clone $today;
        $end = clone $today;

        switch ($goal->getPeriod()) {
            case Goal::PERIOD_WEEKLY:
                $start->modify('monday this week');
                $end->modify('sunday this week');
                break;
            case Goal::PERIOD_MONTHLY:
                $start->modify('first day of this month');
                $end->modify('last day of this month');
                break;
            case Goal::PERIOD_DAILY:
                break;
        }

        $end->setTime(23, 59, 59);

        $currentValue = $this->goalLogRepository->getSumBetweenDates($goal, $start, $end);
        $target = $goal->getTargetValue() ?? 1;
        $percentage = ($target > 0) ? (int) round(($currentValue / $target) * 100) : 0;

        return [
            'current' => $currentValue,
            'target' => $target,
            'percentage' => min($percentage, 100),
            'period_start' => $start,
            'period_end' => $end
        ];
    }

    /**
     * Calcula la racha actual de días consecutivos cumpliendo un objetivo de tipo racha (STREAK).
     * Evalúa si el hábito ya se ha completado hoy para mantener la racha activa o en espera.
     *
     * @param Goal $goal El objetivo de racha a evaluar.
     * @return array<string, mixed> Datos de la racha (días consecutivos y estado de hoy).
     */
    public function getStreak(Goal $goal): array
    {
        if ($goal->getType() !== Goal::TYPE_STREAK) {
            return [];
        }

        $logs = $this->goalLogRepository->findLogsForGoal($goal);

        if (empty($logs)) {
            return [
                'current_streak' => 0,
                'is_completed_today' => false,
            ];
        }

        $streak = 0;
        $today = new \DateTime('today');
        $yesterday = (clone $today)->modify('-1 day');

        $lastLogDate = $logs[0]->getDate()->format('Y-m-d');
        $todayStr = $today->format('Y-m-d');
        $isCompletedToday = ($lastLogDate === $todayStr);

        $checkDate = $isCompletedToday ? clone $today : clone $yesterday;

        // Si el último registro no es ni de hoy ni de ayer, la racha se ha perdido.
        if (!$isCompletedToday && $lastLogDate !== $yesterday->format('Y-m-d')) {
             return [
                 'current_streak' => 0,
                 'is_completed_today' => false,
             ];
        }

        $processedDays = [];

        foreach ($logs as $log) {
            $logDateStr = $log->getDate()->format('Y-m-d');

            // Evitamos sumar más de un registro por día a la racha total.
            if (in_array($logDateStr, $processedDays, true)) {
                continue;
            }

            if ($logDateStr === $checkDate->format('Y-m-d')) {
                $streak++;
                $processedDays[] = $logDateStr;
                $checkDate->modify('-1 day');
            } else {
                break;
            }
        }

        return [
            'current_streak' => $streak,
            'is_completed_today' => $isCompletedToday,
        ];
    }
}
