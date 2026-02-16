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

#[IsGranted('ROLE_USER')]
final class GoalLogController extends AbstractController
{
    #[Route('/goals/{id}/log/new', name: 'app_goal_log_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Goal $goal, GoalLogRepository $goalLogRepository): Response
    {
        // Seguridad: Verificar que el objetivo es del usuario
        if ($goal->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $goalLog = new GoalLog();
        $goalLog->setGoal($goal);
        $goalLog->setDate(new \DateTimeImmutable('today'));

        $form = $this->createForm(GoalLogType::class, $goalLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $goalLogRepository->save($goalLog, true);

            $this->addFlash('success', 'Registro añadido al historial.');

            return $this->redirectToRoute('app_goal_show', ['id' => $goal->getId()]);
        }

        return $this->render('goal_log/new.html.twig', [
            'goal_log' => $goalLog,
            'goal' => $goal,
            'form' => $form,
        ]);
    }

    #[Route('/log/{id}/edit', name: 'app_goal_log_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GoalLog $goalLog, GoalLogRepository $goalLogRepository): Response
    {
        // Seguridad: Verificar a través del objetivo padre
        if ($goalLog->getGoal()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(GoalLogType::class, $goalLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $goalLogRepository->save($goalLog, true);

            $this->addFlash('success', 'Registro corregido.');

            // Redirigimos al objetivo padre
            return $this->redirectToRoute('app_goal_show', ['id' => $goalLog->getGoal()->getId()]);
        }

        return $this->render('goal_log/edit.html.twig', [
            'goal_log' => $goalLog,
            'form' => $form,
        ]);
    }

    #[Route('/log/{id}', name: 'app_goal_log_delete', methods: ['POST'])]
    public function delete(Request $request, GoalLog $goalLog, GoalLogRepository $goalLogRepository): Response
    {
        // Seguridad
        if ($goalLog->getGoal()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Guardamos el ID del padre antes de borrar el hijo para poder redirigir
        $goalId = $goalLog->getGoal()->getId();

        if ($this->isCsrfTokenValid('delete'.$goalLog->getId(), $request->request->get('_token'))) {
            $goalLogRepository->remove($goalLog, true);
            $this->addFlash('success', 'Registro eliminado del historial.');
        }

        return $this->redirectToRoute('app_goal_show', ['id' => $goalId]);
    }
}
