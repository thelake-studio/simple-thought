<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controlador encargado de generar y mostrar el panel de estadísticas avanzadas.
 * Procesa los filtros temporales y delega la recolección de datos al StatsService.
 */
#[Route('/stats')]
#[IsGranted('ROLE_USER')]
final class StatsController extends AbstractController
{
    /**
     * Constructor para la inyección de dependencias.
     *
     * @param StatsService $statsService Servicio que centraliza la lógica matemática y de negocio de las métricas.
     */
    public function __construct(
        private readonly StatsService $statsService
    ) {
    }

    /**
     * Muestra el Dashboard principal de estadísticas y analíticas.
     * Captura los parámetros de fecha de la URL para filtrar los datos dinámicamente.
     *
     * @param Request $request La petición HTTP que contiene los parámetros GET ('start', 'end').
     * @return Response La vista renderizada con todas las gráficas y métricas empaquetadas.
     */
    #[Route('/', name: 'app_stats_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        // Verificación de seguridad: Aseguramos que el usuario logueado es nuestra entidad User válida
        if (!$user instanceof User) {
            $this->addFlash('error', 'Sesión de usuario no válida o expirada.');

            return $this->redirectToRoute('app_login');
        }

        $startString = $request->query->get('start');
        $endString = $request->query->get('end');

        // Intento de parseo de las fechas; si fallan o no existen, se aplican valores por defecto
        try {
            $startDate = $startString ? new \DateTime($startString) : new \DateTime('-6 days');
            $endDate = $endString ? new \DateTime($endString) : new \DateTime('today');
        } catch (\Exception $e) {
            $startDate = new \DateTime('-6 days');
            $endDate = new \DateTime('today');
        }

        // Normalización de horas para asegurar que cubren los días completos
        $startDate->setTime(0, 0, 0);
        $endDate->setTime(23, 59, 59);

        return $this->render('stats/index.html.twig', [
            'currentStart' => $startDate->format('Y-m-d'),
            'currentEnd' => $endDate->format('Y-m-d'),
            'moodEvolutionData' => $this->statsService->getMoodEvolutionData($user, $startDate, $endDate),
            'topActivitiesData' => $this->statsService->getTopActivitiesData($user, $startDate, $endDate),
            'activityMoodMatrixData' => $this->statsService->getActivityMoodMatrixData($user, $startDate, $endDate),
            'goalMoodCorrelationData' => $this->statsService->getGoalMoodCorrelationData($user, $startDate, $endDate),
            'tagContextData' => $this->statsService->getTagContextData($user, $startDate, $endDate),
            'yearInPixelsData' => $this->statsService->getYearInPixelsData($user, $startDate),
        ]);
    }
}
