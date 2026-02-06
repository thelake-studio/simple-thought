<?php

namespace App\Controller;

use App\Entity\Entry;
use App\Form\EntryType;
use App\Repository\ActivityRepository;
use App\Repository\EmotionRepository;
use App\Repository\EntryRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/entry')]
#[IsGranted('ROLE_USER')]
final class EntryController extends AbstractController
{
    #[Route('/', name: 'app_entry_index', methods: ['GET'])]
    public function index(EntryRepository $entryRepository): Response
    {
        return $this->render('entry/index.html.twig', [
            'entries' => $entryRepository->findAllByUser($this->getUser()),
        ]);
    }

    #[Route('/new', name: 'app_entry_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntryRepository $entryRepository,
        EmotionRepository $emotionRepository,
        ActivityRepository $activityRepository,
        TagRepository $tagRepository
    ): Response
    {
        $user = $this->getUser();

        $entry = new Entry();
        $entry->setDate(new \DateTime());

        $form = $this->createForm(EntryType::class, $entry, [
            'emotions' => $emotionRepository->findAllByUser($user),
            'activities' => $activityRepository->findAllByUser($user),
            'tags' => $tagRepository->findAllByUser($user),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entry->setUser($user);
            $entry->setCreatedAt(new \DateTimeImmutable());

            if ($entry->getEmotion()) {
                $entry->setMoodValueSnapshot($entry->getEmotion()->getValue());
            }

            $entryRepository->save($entry, true);

            $this->addFlash('success', '¡Entrada guardada correctamente!');

            return $this->redirectToRoute('app_entry_index');
        }

        return $this->render('entry/new.html.twig', [
            'entry' => $entry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_entry_show', methods: ['GET'])]
    public function show(Entry $entry): Response
    {
        if ($entry->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('entry/show.html.twig', [
            'entry' => $entry,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_entry_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Entry $entry,
        EntryRepository $entryRepository,
        EmotionRepository $emotionRepository,
        ActivityRepository $activityRepository,
        TagRepository $tagRepository
    ): Response
    {
        if ($entry->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();

        $form = $this->createForm(EntryType::class, $entry, [
            'emotions' => $emotionRepository->findAllByUser($user),
            'activities' => $activityRepository->findAllByUser($user),
            'tags' => $tagRepository->findAllByUser($user),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($entry->getEmotion()) {
                $entry->setMoodValueSnapshot($entry->getEmotion()->getValue());
            }

            $entryRepository->save($entry, true);

            $this->addFlash('success', 'Entrada actualizada.');

            return $this->redirectToRoute('app_entry_index');
        }

        return $this->render('entry/edit.html.twig', [
            'entry' => $entry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_entry_delete', methods: ['POST'])]
    public function delete(Request $request, Entry $entry, EntryRepository $entryRepository): Response
    {
        if ($entry->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$entry->getId(), $request->request->get('_token'))) {
            $entryRepository->remove($entry, true);
            $this->addFlash('success', 'Entrada eliminada.');
        }

        return $this->redirectToRoute('app_entry_index');
    }
}
