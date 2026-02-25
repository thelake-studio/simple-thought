<?php

namespace App\Controller;

use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/stats')]
#[IsGranted('ROLE_USER')]
final class StatsController extends AbstractController
{
    #[Route('/', name: 'app_stats_index', methods: ['GET'])]
    public function index(Request $request, StatsService $statsService): Response
    {
        $user = $this->getUser();

        $startString = $request->query->get('start');
        $endString = $request->query->get('end');

        try {
            $startDate = $startString ? new \DateTime($startString) : new \DateTime('-6 days');
            $endDate = $endString ? new \DateTime($endString) : new \DateTime('today');
        } catch (\Exception $e) {
            $startDate = new \DateTime('-6 days');
            $endDate = new \DateTime('today');
        }

        $startDate->setTime(0, 0, 0);
        $endDate->setTime(23, 59, 59);

        // 3. PASAR LOS DATOS A TWIG
        return $this->render('stats/index.html.twig', [
            'currentStart' => $startDate->format('Y-m-d'),
            'currentEnd' => $endDate->format('Y-m-d'),
            'moodEvolutionData' => $statsService->getMoodEvolutionData($user, $startDate, $endDate),
            'topActivitiesData' => $statsService->getTopActivitiesData($user, $startDate, $endDate),
            'activityMoodMatrixData' => $statsService->getActivityMoodMatrixData($user, $startDate, $endDate),
        ]);
    }
}
