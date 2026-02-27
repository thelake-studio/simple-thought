<?php

namespace App\Controller;

use App\Entity\Emotion;
use App\Form\EmotionType;
use App\Repository\EmotionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controlador encargado de gestionar el CRUD de las emociones (Catálogo del usuario).
 * Permite listar, crear, visualizar, editar y eliminar emociones personalizadas.
 */
#[Route('/emotion')]
#[IsGranted('ROLE_USER')]
final class EmotionController extends AbstractController
{
    /**
     * Constructor para inyectar las dependencias necesarias.
     *
     * @param EmotionRepository $emotionRepository Repositorio para gestionar la base de datos de emociones.
     */
    public function __construct(
        private readonly EmotionRepository $emotionRepository
    ) {
    }

    /**
     * Muestra la lista de todas las emociones pertenecientes al usuario autenticado.
     *
     * @return Response La vista renderizada con la tabla de emociones.
     */
    #[Route('/', name: 'app_emotion_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('emotion/index.html.twig', [
            'emotions' => $this->emotionRepository->findAllByUser($this->getUser()),
        ]);
    }

    /**
     * Muestra y procesa el formulario para crear una nueva emoción.
     *
     * @param Request $request La petición HTTP con los datos del formulario.
     * @return Response Redirección al índice si tiene éxito, o la vista del formulario si no.
     */
    #[Route('/new', name: 'app_emotion_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $emotion = new Emotion();
        $form = $this->createForm(EmotionType::class, $emotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emotion->setUser($this->getUser());
            $this->emotionRepository->save($emotion, true);

            $this->addFlash('success', 'Nueva emoción añadida correctamente.');

            return $this->redirectToRoute('app_emotion_index');
        }

        return $this->render('emotion/new.html.twig', [
            'emotion' => $emotion,
            'form' => $form,
        ]);
    }

    /**
     * Muestra el detalle de una emoción específica.
     * Protege contra ataques IDOR verificando que la emoción pertenece al usuario actual.
     *
     * @param Emotion $emotion La emoción solicitada (inyectada por el ParamConverter).
     * @return Response La vista de detalle o redirección segura si no hay permisos.
     */
    #[Route('/{id}', name: 'app_emotion_show', methods: ['GET'])]
    public function show(Emotion $emotion): Response
    {
        if ($emotion->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para ver esta emoción.');

            return $this->redirectToRoute('app_emotion_index');
        }

        return $this->render('emotion/show.html.twig', [
            'emotion' => $emotion,
        ]);
    }

    /**
     * Muestra y procesa el formulario de edición de una emoción.
     *
     * @param Request $request La petición HTTP.
     * @param Emotion $emotion La emoción a editar.
     * @return Response Redirección al índice si tiene éxito, o la vista del formulario si no.
     */
    #[Route('/{id}/edit', name: 'app_emotion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Emotion $emotion): Response
    {
        if ($emotion->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para editar esta emoción.');

            return $this->redirectToRoute('app_emotion_index');
        }

        $form = $this->createForm(EmotionType::class, $emotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->emotionRepository->save($emotion, true);

            $this->addFlash('success', 'Emoción actualizada correctamente.');

            return $this->redirectToRoute('app_emotion_index');
        }

        return $this->render('emotion/edit.html.twig', [
            'emotion' => $emotion,
            'form' => $form,
        ]);
    }

    /**
     * Elimina una emoción de la base de datos de forma segura.
     * Valida el token CSRF y la propiedad de la entidad.
     *
     * @param Request $request La petición HTTP para validar el token.
     * @param Emotion $emotion La emoción a eliminar.
     * @return Response Redirección al índice de emociones tras la operación.
     */
    #[Route('/{id}', name: 'app_emotion_delete', methods: ['POST'])]
    public function delete(Request $request, Emotion $emotion): Response
    {
        if ($emotion->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para eliminar esta emoción.');

            return $this->redirectToRoute('app_emotion_index');
        }

        // Validación del token CSRF del formulario oculto en la vista
        if ($this->isCsrfTokenValid('delete'.$emotion->getId(), (string) $request->request->get('_token'))) {
            $this->emotionRepository->remove($emotion, true);
            $this->addFlash('success', 'Emoción eliminada con éxito.');
        } else {
            $this->addFlash('error', 'Token de seguridad inválido. No se pudo eliminar.');
        }

        return $this->redirectToRoute('app_emotion_index');
    }
}
