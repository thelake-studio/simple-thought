<?php

namespace App\Controller;

use App\Entity\Goal;
use App\Repository\GoalRepository;
use App\Service\GoalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/goals')]
#[IsGranted('ROLE_USER')]
final class GoalController extends AbstractController
{
    #[Route('/', name: 'app_goal_index', methods: ['GET'])]
    public function index(GoalRepository $goalRepository, GoalService $goalService): Response
    {
        $goals = $goalRepository->findByUser($this->getUser());

        $dashboardData = [];

        foreach ($goals as $goal) {
            if ($goal->getType() === Goal::TYPE_SUM) {
                $stats = $goalService->getProgress($goal);
            } else {
                $stats = $goalService->getStreak($goal);
            }

            $dashboardData[] = [
                'entity' => $goal,
                'stats' => $stats
            ];
        }

        return $this->render('goal/index.html.twig', [
            'goals_data' => $dashboardData,
        ]);
    }
}
