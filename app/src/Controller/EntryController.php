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

/**
 * Controlador principal encargado de gestionar el CRUD del Diario Emocional (Entries).
 * Gestiona la creación de entradas cruzando datos de los catálogos del usuario.
 */
#[Route('/entry')]
#[IsGranted('ROLE_USER')]
final class EntryController extends AbstractController
{
    /**
     * Inyección de dependencias centralizada para todos los repositorios necesarios.
     *
     * @param EntryRepository $entryRepository Repositorio de las entradas.
     * @param EmotionRepository $emotionRepository Repositorio de las emociones del usuario.
     * @param ActivityRepository $activityRepository Repositorio de las actividades del usuario.
     * @param TagRepository $tagRepository Repositorio de las etiquetas del usuario.
     */
    public function __construct(
        private readonly EntryRepository $entryRepository,
        private readonly EmotionRepository $emotionRepository,
        private readonly ActivityRepository $activityRepository,
        private readonly TagRepository $tagRepository
    ) {
    }

    /**
     * Muestra el listado del diario (Timeline) del usuario autenticado.
     *
     * @return Response La vista renderizada con el historial de entradas.
     */
    #[Route('/', name: 'app_entry_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('entry/index.html.twig', [
            'entries' => $this->entryRepository->findAllByUser($this->getUser()),
        ]);
    }

    /**
     * Despliega y procesa el formulario de una nueva entrada en el diario.
     * Alimenta el formulario con los catálogos personalizados del usuario.
     *
     * @param Request $request La petición HTTP con los datos del formulario.
     * @return Response Redirección al índice si tiene éxito, o vista del formulario si falla/inicia.
     */
    #[Route('/new', name: 'app_entry_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        $entry = new Entry();
        $entry->setDate(new \DateTime());

        $form = $this->createForm(EntryType::class, $entry, [
            'emotions' => $this->emotionRepository->findAllByUser($user),
            'activities' => $this->activityRepository->findAllByUser($user),
            'tags' => $this->tagRepository->findAllByUser($user),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entry->setUser($user);
            $entry->setCreatedAt(new \DateTimeImmutable());

            // Capturamos el valor histórico del ánimo (Snapshot) para evitar que cambie
            // si el usuario edita la emoción maestra en el futuro.
            if ($entry->getEmotion()) {
                $entry->setMoodValueSnapshot($entry->getEmotion()->getValue());
            }

            $this->entryRepository->save($entry, true);

            $this->addFlash('success', '¡Reflexión guardada correctamente!');

            return $this->redirectToRoute('app_entry_index');
        }

        return $this->render('entry/new.html.twig', [
            'entry' => $entry,
            'form' => $form,
        ]);
    }

    /**
     * Muestra el detalle completo de una entrada específica del diario.
     *
     * @param Entry $entry La entrada solicitada.
     * @return Response La vista de detalle o redirección segura si se detecta IDOR.
     */
    #[Route('/{id}', name: 'app_entry_show', methods: ['GET'])]
    public function show(Entry $entry): Response
    {
        if ($entry->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para visualizar esta entrada.');

            return $this->redirectToRoute('app_entry_index');
        }

        return $this->render('entry/show.html.twig', [
            'entry' => $entry,
        ]);
    }

    /**
     * Muestra y procesa la edición de una entrada existente.
     *
     * @param Request $request La petición HTTP.
     * @param Entry $entry La entrada a editar.
     * @return Response Redirección tras editar, o formulario repoblado.
     */
    #[Route('/{id}/edit', name: 'app_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Entry $entry): Response
    {
        if ($entry->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para editar esta entrada.');

            return $this->redirectToRoute('app_entry_index');
        }

        $user = $this->getUser();

        $form = $this->createForm(EntryType::class, $entry, [
            'emotions' => $this->emotionRepository->findAllByUser($user),
            'activities' => $this->activityRepository->findAllByUser($user),
            'tags' => $this->tagRepository->findAllByUser($user),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Actualizamos el snapshot por si el usuario ha cambiado de emoción principal
            if ($entry->getEmotion()) {
                $entry->setMoodValueSnapshot($entry->getEmotion()->getValue());
            }

            $this->entryRepository->save($entry, true);

            $this->addFlash('success', 'Entrada del diario actualizada.');

            return $this->redirectToRoute('app_entry_index');
        }

        return $this->render('entry/edit.html.twig', [
            'entry' => $entry,
            'form' => $form,
        ]);
    }

    /**
     * Gestiona el borrado seguro de una entrada del diario.
     *
     * @param Request $request La petición HTTP con el token CSRF.
     * @param Entry $entry La entrada que se va a destruir.
     * @return Response Redirección al índice tras el borrado o fallo de validación.
     */
    #[Route('/{id}', name: 'app_entry_delete', methods: ['POST'])]
    public function delete(Request $request, Entry $entry): Response
    {
        if ($entry->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para eliminar esta entrada.');

            return $this->redirectToRoute('app_entry_index');
        }

        if ($this->isCsrfTokenValid('delete'.$entry->getId(), (string) $request->request->get('_token'))) {
            $this->entryRepository->remove($entry, true);
            $this->addFlash('success', 'Entrada eliminada correctamente del diario.');
        } else {
            $this->addFlash('error', 'Token de seguridad inválido. No se pudo eliminar la entrada.');
        }

        return $this->redirectToRoute('app_entry_index');
    }
}
