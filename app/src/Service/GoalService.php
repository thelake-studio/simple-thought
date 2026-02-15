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

        return [
            'current' => 0,
            'target' => $goal->getTargetValue(),
            'percentage' => 0,
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
