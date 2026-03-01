<?php

namespace App\Controller;

use App\Entity\Goal;
use App\Entity\GoalLog;
use App\Form\GoalLogType;
use App\Repository\GoalLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controlador encargado de gestionar el CRUD de los registros individuales de progreso (GoalLogs).
 * Permite añadir, editar y eliminar entradas específicas en el historial de un objetivo.
 */
#[IsGranted('ROLE_USER')]
final class GoalLogController extends AbstractController
{
    /**
     * Constructor para la inyección de dependencias.
     *
     * @param GoalLogRepository $goalLogRepository Repositorio para la gestión de registros de progreso.
     */
    public function __construct(
        private readonly GoalLogRepository $goalLogRepository
    ) {
    }

    /**
     * Muestra y procesa el formulario para añadir un nuevo registro de progreso a un objetivo específico.
     *
     * @param Request $request La petición HTTP.
     * @param Goal $goal El objetivo al que se le añadirá el registro.
     * @return Response Redirección a la vista del objetivo o renderizado del formulario.
     */
    #[Route('/goals/{id}/log/new', name: 'app_goal_log_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Goal $goal): Response
    {
        if ($goal->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para añadir registros a este objetivo.');

            return $this->redirectToRoute('app_goal_index');
        }

        $goalLog = new GoalLog();
        $goalLog->setGoal($goal);
        $goalLog->setDate(new \DateTimeImmutable('today'));

        $form = $this->createForm(GoalLogType::class, $goalLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->goalLogRepository->save($goalLog, true);

            $this->addFlash('success', 'Registro añadido al historial.');

            return $this->redirectToRoute('app_goal_show', ['id' => $goal->getId()]);
        }

        return $this->render('goal_log/new.html.twig', [
            'goal_log' => $goalLog,
            'goal' => $goal,
            'form' => $form,
        ]);
    }

    /**
     * Muestra y procesa la edición de un registro de progreso existente.
     *
     * @param Request $request La petición HTTP.
     * @param GoalLog $goalLog El registro a editar.
     * @return Response Redirección a la vista del objetivo padre o renderizado del formulario.
     */
    #[Route('/log/{id}/edit', name: 'app_goal_log_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GoalLog $goalLog): Response
    {
        if ($goalLog->getGoal()->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para editar este registro.');

            return $this->redirectToRoute('app_goal_index');
        }

        $form = $this->createForm(GoalLogType::class, $goalLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->goalLogRepository->save($goalLog, true);

            $this->addFlash('success', 'Registro corregido correctamente.');

            return $this->redirectToRoute('app_goal_show', ['id' => $goalLog->getGoal()->getId()]);
        }

        return $this->render('goal_log/edit.html.twig', [
            'goal_log' => $goalLog,
            'form' => $form,
        ]);
    }

    /**
     * Elimina un registro de progreso del historial de forma segura.
     *
     * @param Request $request Petición HTTP para validar el token CSRF.
     * @param GoalLog $goalLog El registro a eliminar.
     * @return Response Redirección a la vista de detalle del objetivo padre.
     */
    #[Route('/log/{id}', name: 'app_goal_log_delete', methods: ['POST'])]
    public function delete(Request $request, GoalLog $goalLog): Response
    {
        if ($goalLog->getGoal()->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para eliminar este registro.');

            return $this->redirectToRoute('app_goal_index');
        }

        $goalId = $goalLog->getGoal()->getId();

        if ($this->isCsrfTokenValid('delete'.$goalLog->getId(), (string) $request->request->get('_token'))) {
            $this->goalLogRepository->remove($goalLog, true);
            $this->addFlash('success', 'Registro eliminado del historial.');
        } else {
            $this->addFlash('error', 'Token de seguridad inválido. No se pudo eliminar.');
        }

        return $this->redirectToRoute('app_goal_show', ['id' => $goalId]);
    }
}
