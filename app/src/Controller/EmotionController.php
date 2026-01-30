<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EmotionController extends AbstractController
{
    #[Route('/emotion', name: 'app_emotion')]
    public function index(): Response
    {
        return $this->render('emotion/index.html.twig', [
            'controller_name' => 'EmotionController',
        ]);
    }
}
