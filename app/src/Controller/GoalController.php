<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GoalController extends AbstractController
{
    #[Route('/goal', name: 'app_goal')]
    public function index(): Response
    {
        return $this->render('goal/index.html.twig', [
            'controller_name' => 'GoalController',
        ]);
    }
}
