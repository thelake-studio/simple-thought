<?php

namespace App\Controller;

use App\Repository\EmotionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/emotion')]
#[IsGranted('ROLE_USER')]
final class EmotionController extends AbstractController
{
    #[Route('/', name: 'app_emotion_index', methods: ['GET'])]
    public function index(EmotionRepository $emotionRepository): Response
    {
        return $this->render('emotion/index.html.twig', [
            'emotions' => $emotionRepository->findAllByUser($this->getUser()),
        ]);
    }
}
