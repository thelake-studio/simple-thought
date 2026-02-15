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

        return [
            'current_streak' => 0,
            'is_completed_today' => false,
        ];
    }
}
