<?php

namespace App\Controller;

use App\Entity\Goal;
use App\Form\GoalType;
use App\Repository\GoalRepository;
use App\Service\GoalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/new', name: 'app_goal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, GoalRepository $goalRepository): Response
    {
        $goal = new Goal();
        $form = $this->createForm(GoalType::class, $goal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $goal->setUser($this->getUser());
            // Guardamos la fecha de creación automáticamente si no está en el constructor
            $goal->setCreatedAt(new \DateTimeImmutable());

            $goalRepository->save($goal, true);

            $this->addFlash('success', '¡Nuevo objetivo definido! A por ello.');

            return $this->redirectToRoute('app_goal_index');
        }

        return $this->render('goal/new.html.twig', [
            'goal' => $goal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_goal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Goal $goal, GoalRepository $goalRepository): Response
    {
        if ($goal->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(GoalType::class, $goal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $goalRepository->save($goal, true);

            $this->addFlash('success', 'Objetivo actualizado.');

            return $this->redirectToRoute('app_goal_index');
        }

        return $this->render('goal/edit.html.twig', [
            'goal' => $goal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_goal_delete', methods: ['POST'])]
    public function delete(Request $request, Goal $goal, GoalRepository $goalRepository): Response
    {
        if ($goal->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$goal->getId(), $request->request->get('_token'))) {
            $goalRepository->remove($goal, true);
            $this->addFlash('success', 'Objetivo eliminado.');
        }

        return $this->redirectToRoute('app_goal_index');
    }
}
