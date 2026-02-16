<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GoalLogController extends AbstractController
{
    #[Route('/goal/log', name: 'app_goal_log')]
    public function index(): Response
    {
        return $this->render('goal_log/index.html.twig', [
            'controller_name' => 'GoalLogController',
        ]);
    }
}
