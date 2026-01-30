<?php

namespace App\Controller;

use App\Entity\Emotion;
use App\Form\EmotionType;
use App\Repository\EmotionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/new', name: 'app_emotion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EmotionRepository $emotionRepository): Response
    {
        $emotion = new Emotion();
        $form = $this->createForm(EmotionType::class, $emotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emotion->setUser($this->getUser());
            $emotionRepository->save($emotion, true);

            $this->addFlash('success', 'Nueva emoción añadida.');

            return $this->redirectToRoute('app_emotion_index');
        }

        return $this->render('emotion/new.html.twig', [
            'emotion' => $emotion,
            'form' => $form,
        ]);
    }
}
