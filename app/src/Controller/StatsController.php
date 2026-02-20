<?php

namespace App\Controller;

use App\Repository\EntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/stats')]
#[IsGranted('ROLE_USER')]
final class StatsController extends AbstractController
{
    #[Route('/', name: 'app_stats_index', methods: ['GET'])]
    public function index(EntryRepository $entryRepository): Response
    {
        // 1. Obtenemos el usuario (garantizado por IsGranted)
        $user = $this->getUser();

        // 2. Calculamos las fechas: Lunes y Domingo de esta semana
        $monday = new \DateTime('monday this week');
        $sunday = new \DateTime('sunday this week');

        // 3. Obtenemos las entradas reales de la base de datos
        $entries = $entryRepository->findEntriesBetweenDates($user, $monday, $sunday);

        // 4. Preparamos los datos base para la semana (de Lunes a Domingo)
        $weeklyData = [
            'Lunes' => [], 'Martes' => [], 'Miércoles' => [],
            'Jueves' => [], 'Viernes' => [], 'Sábado' => [], 'Domingo' => []
        ];

        // 5. Rellenamos con los datos reales
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

        // 6. Calculamos la media por día
        $chartData = [];
        foreach ($weeklyData as $day => $values) {
            if (count($values) > 0) {
                $chartData[] = array_sum($values) / count($values);
            } else {
                $chartData[] = null; // Día sin datos
            }
        }

        // 7. Formato para Chart.js
        $moodChartData = [
            'labels' => array_keys($weeklyData),
            'datasets' => [
                [
                    'label' => 'Nivel de Ánimo',
                    'data' => $chartData,
                    'borderColor' => '#0d6efd',
                    'backgroundColor' => 'rgba(13, 110, 253, 0.2)',
                    'tension' => 0.4,
                    'fill' => true,
                    'spanGaps' => true
                ]
            ]
        ];

        return $this->render('stats/index.html.twig', [
            'moodChartData' => $moodChartData,
        ]);
    }
}
