<?php

namespace App\Controller;

use App\Entity\Goal;
use App\Entity\GoalLog;
use App\Form\GoalType;
use App\Repository\GoalLogRepository;
use App\Repository\GoalRepository;
use App\Service\GoalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controlador encargado de gestionar el CRUD y las interacciones de los objetivos (Goals).
 * Permite al usuario crear metas, visualizar su progreso y registrar nuevos avances.
 */
#[Route('/goals')]
#[IsGranted('ROLE_USER')]
final class GoalController extends AbstractController
{
    /**
     * Constructor para la inyección de dependencias centralizada.
     *
     * @param GoalRepository $goalRepository Repositorio de objetivos.
     * @param GoalLogRepository $goalLogRepository Repositorio de registros de progreso.
     * @param GoalService $goalService Servicio con la lógica de cálculo de rachas y progresos.
     */
    public function __construct(
        private readonly GoalRepository $goalRepository,
        private readonly GoalLogRepository $goalLogRepository,
        private readonly GoalService $goalService
    ) {
    }

    /**
     * Muestra el panel principal de objetivos del usuario, calculando el progreso actual de cada uno.
     *
     * @return Response La vista renderizada con el Dashboard de objetivos.
     */
    #[Route('/', name: 'app_goal_index', methods: ['GET'])]
    public function index(): Response
    {
        $goals = $this->goalRepository->findByUser($this->getUser());
        $dashboardData = [];

        foreach ($goals as $goal) {
            if ($goal->getType() === Goal::TYPE_SUM) {
                $stats = $this->goalService->getProgress($goal);
            } else {
                $stats = $this->goalService->getStreak($goal);
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

    /**
     * Despliega y procesa el formulario para crear un nuevo objetivo.
     *
     * @param Request $request Petición HTTP con los datos del formulario.
     * @return Response Redirección al índice si tiene éxito, o vista del formulario en caso contrario.
     */
    #[Route('/new', name: 'app_goal_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $goal = new Goal();
        $form = $this->createForm(GoalType::class, $goal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $goal->setUser($this->getUser());
            $goal->setCreatedAt(new \DateTimeImmutable());

            $this->goalRepository->save($goal, true);

            $this->addFlash('success', '¡Nuevo objetivo definido! A por ello.');

            return $this->redirectToRoute('app_goal_index');
        }

        return $this->render('goal/new.html.twig', [
            'goal' => $goal,
            'form' => $form,
        ]);
    }

    /**
     * Registra un nuevo avance (log) para un objetivo específico desde el Dashboard.
     *
     * @param Request $request Petición HTTP con los datos del avance y token CSRF.
     * @param Goal $goal Objetivo al que se le aplicará el avance.
     * @return Response Redirección al índice de objetivos.
     */
    #[Route('/{id}/log', name: 'app_goal_log', methods: ['POST'])]
    public function log(Request $request, Goal $goal): Response
    {
        if ($goal->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para registrar progreso en este objetivo.');

            return $this->redirectToRoute('app_goal_index');
        }

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('log-goal'.$goal->getId(), (string) $token)) {
             $this->addFlash('error', 'Token de seguridad inválido. Inténtalo de nuevo.');

             return $this->redirectToRoute('app_goal_index');
        }

        $value = $request->request->get('value');
        $amount = is_numeric($value) ? (int) $value : 1;

        $log = new GoalLog();
        $log->setGoal($goal);
        $log->setDate(new \DateTimeImmutable('today'));
        $log->setValue($amount);

        $this->goalLogRepository->save($log, true);

        $this->addFlash('success', '¡Progreso registrado! Sigue así.');

        return $this->redirectToRoute('app_goal_index');
    }

    /**
     * Muestra y procesa la edición de un objetivo existente.
     *
     * @param Request $request Petición HTTP con los datos del formulario.
     * @param Goal $goal Objetivo a editar.
     * @return Response Redirección tras la edición o renderizado del formulario.
     */
    #[Route('/{id}/edit', name: 'app_goal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Goal $goal): Response
    {
        if ($goal->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para editar este objetivo.');

            return $this->redirectToRoute('app_goal_index');
        }

        $form = $this->createForm(GoalType::class, $goal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->goalRepository->save($goal, true);

            $this->addFlash('success', 'Objetivo actualizado.');

            return $this->redirectToRoute('app_goal_index');
        }

        return $this->render('goal/edit.html.twig', [
            'goal' => $goal,
            'form' => $form,
        ]);
    }

    /**
     * Muestra los detalles de un objetivo, incluyendo su histórico completo de avances.
     *
     * @param Goal $goal El objetivo a visualizar.
     * @return Response Vista con los detalles y estadísticas del objetivo.
     */
    #[Route('/{id}', name: 'app_goal_show', methods: ['GET'])]
    public function show(Goal $goal): Response
    {
        if ($goal->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para visualizar este objetivo.');

            return $this->redirectToRoute('app_goal_index');
        }

        if ($goal->getType() === Goal::TYPE_SUM) {
            $stats = $this->goalService->getProgress($goal);
        } else {
            $stats = $this->goalService->getStreak($goal);
        }

        $history = $this->goalLogRepository->findLogsForGoal($goal);

        return $this->render('goal/show.html.twig', [
            'goal' => $goal,
            'stats' => $stats,
            'history' => $history,
        ]);
    }

    /**
     * Elimina de forma segura un objetivo y todo su progreso asociado en cascada.
     *
     * @param Request $request Petición HTTP para la validación del token CSRF.
     * @param Goal $goal El objetivo a eliminar.
     * @return Response Redirección al índice de objetivos.
     */
    #[Route('/{id}', name: 'app_goal_delete', methods: ['POST'])]
    public function delete(Request $request, Goal $goal): Response
    {
        if ($goal->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para eliminar este objetivo.');

            return $this->redirectToRoute('app_goal_index');
        }

        if ($this->isCsrfTokenValid('delete'.$goal->getId(), (string) $request->request->get('_token'))) {
            $this->goalRepository->remove($goal, true);
            $this->addFlash('success', 'Objetivo eliminado correctamente.');
        } else {
            $this->addFlash('error', 'Token de seguridad inválido. No se pudo eliminar.');
        }

        return $this->redirectToRoute('app_goal_index');
    }
}
