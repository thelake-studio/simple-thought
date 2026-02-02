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

    #[Route('/{id}/edit', name: 'app_emotion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Emotion $emotion, EmotionRepository $emotionRepository): Response
    {
        // 1. Seguridad: Verificar que la emoción pertenece al usuario
        if ($emotion->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('No puedes editar una emoción que no es tuya.');
        }

        // 2. Crear el formulario con los datos existentes ($emotion)
        $form = $this->createForm(EmotionType::class, $emotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 3. Guardar cambios (Repository Pattern)
            $emotionRepository->save($emotion, true);

            $this->addFlash('success', 'Emoción actualizada correctamente.');

            return $this->redirectToRoute('app_emotion_index');
        }

        return $this->render('emotion/edit.html.twig', [
            'emotion' => $emotion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_emotion_show', methods: ['GET'])]
    public function show(Emotion $emotion): Response
    {
        // 1. Seguridad: Solo el dueño puede ver el detalle
        if ($emotion->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('No puedes ver una emoción que no es tuya.');
        }

        return $this->render('emotion/show.html.twig', [
            'emotion' => $emotion,
        ]);
    }

    #[Route('/{id}', name: 'app_emotion_delete', methods: ['POST'])]
    public function delete(Request $request, \App\Entity\Emotion $emotion, EmotionRepository $emotionRepository): Response
    {
        // Seguridad: Verificar propiedad
        if ($emotion->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$emotion->getId(), $request->request->get('_token'))) {
            $emotionRepository->remove($emotion, true);
            $this->addFlash('success', 'Emoción eliminada.');
        }

        return $this->redirectToRoute('app_emotion_index');
    }
}
