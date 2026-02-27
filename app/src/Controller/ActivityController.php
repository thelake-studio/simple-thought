<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controlador encargado de gestionar el CRUD de las actividades (Catálogo del usuario).
 * Permite listar, crear, visualizar, editar y eliminar actividades personalizadas.
 */
#[Route('/activity')]
#[IsGranted('ROLE_USER')]
final class ActivityController extends AbstractController
{
    /**
     * Constructor para inyectar las dependencias necesarias.
     *
     * @param ActivityRepository $activityRepository Repositorio para gestionar la base de datos de actividades.
     */
    public function __construct(
        private readonly ActivityRepository $activityRepository
    ) {
    }

    /**
     * Muestra la lista de todas las actividades pertenecientes al usuario autenticado.
     *
     * @return Response La vista renderizada con la tabla de actividades.
     */
    #[Route('/', name: 'app_activity_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('activity/index.html.twig', [
            'activities' => $this->activityRepository->findAllByUser($this->getUser()),
        ]);
    }

    /**
     * Muestra y procesa el formulario para crear una nueva actividad.
     *
     * @param Request $request La petición HTTP con los datos del formulario.
     * @return Response Redirección al índice si tiene éxito, o la vista del formulario si no.
     */
    #[Route('/new', name: 'app_activity_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activity->setUser($this->getUser());
            $this->activityRepository->save($activity, true);

            $this->addFlash('success', 'Nueva actividad añadida correctamente.');

            return $this->redirectToRoute('app_activity_index');
        }

        return $this->render('activity/new.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    /**
     * Muestra el detalle de una actividad específica.
     * Protege contra ataques IDOR verificando que la actividad pertenece al usuario actual.
     *
     * @param Activity $activity La actividad solicitada (inyectada por el ParamConverter).
     * @return Response La vista de detalle o redirección segura si no hay permisos.
     */
    #[Route('/{id}', name: 'app_activity_show', methods: ['GET'])]
    public function show(Activity $activity): Response
    {
        if ($activity->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para ver esta actividad.');

            return $this->redirectToRoute('app_activity_index');
        }

        return $this->render('activity/show.html.twig', [
            'activity' => $activity,
        ]);
    }

    /**
     * Muestra y procesa el formulario de edición de una actividad.
     *
     * @param Request $request La petición HTTP.
     * @param Activity $activity La actividad a editar.
     * @return Response Redirección al índice si tiene éxito, o la vista del formulario si no.
     */
    #[Route('/{id}/edit', name: 'app_activity_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Activity $activity): Response
    {
        if ($activity->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para editar esta actividad.');

            return $this->redirectToRoute('app_activity_index');
        }

        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->activityRepository->save($activity, true);

            $this->addFlash('success', 'Actividad actualizada correctamente.');

            return $this->redirectToRoute('app_activity_index');
        }

        return $this->render('activity/edit.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    /**
     * Elimina una actividad de la base de datos de forma segura.
     * Valida el token CSRF y la propiedad de la entidad.
     *
     * @param Request $request La petición HTTP para validar el token.
     * @param Activity $activity La actividad a eliminar.
     * @return Response Redirección al índice de actividades tras la operación.
     */
    #[Route('/{id}', name: 'app_activity_delete', methods: ['POST'])]
    public function delete(Request $request, Activity $activity): Response
    {
        if ($activity->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para eliminar esta actividad.');

            return $this->redirectToRoute('app_activity_index');
        }

        // Validación del token CSRF del formulario oculto en la vista
        if ($this->isCsrfTokenValid('delete'.$activity->getId(), (string) $request->request->get('_token'))) {
            $this->activityRepository->remove($activity, true);
            $this->addFlash('success', 'Actividad eliminada con éxito.');
        } else {
            $this->addFlash('error', 'Token de seguridad inválido. No se pudo eliminar.');
        }

        return $this->redirectToRoute('app_activity_index');
    }
}
