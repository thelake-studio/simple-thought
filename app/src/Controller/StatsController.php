<?php

namespace App\Controller;

use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/stats')]
#[IsGranted('ROLE_USER')]
final class StatsController extends AbstractController
{
    #[Route('/', name: 'app_stats_index', methods: ['GET'])]
    public function index(StatsService $statsService): Response
    {
        $user = $this->getUser();

        return $this->render('stats/index.html.twig', [
            'moodChartData' => $statsService->getWeeklyMoodData($user),
            'monthlyChartData' => $statsService->getMonthlyMoodData($user),
            'topActivitiesData' => $statsService->getTopActivitiesData($user),
            'activityMoodMatrixData' => $statsService->getActivityMoodMatrixData($user),
        ]);
    }
}
