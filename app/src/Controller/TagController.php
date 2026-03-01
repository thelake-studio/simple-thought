<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controlador encargado de gestionar el CRUD de las etiquetas (Tags).
 * Permite listar, crear, visualizar, editar y eliminar etiquetas personalizadas del usuario.
 */
#[Route('/tag')]
#[IsGranted('ROLE_USER')]
final class TagController extends AbstractController
{
    /**
     * Constructor para inyectar las dependencias necesarias.
     *
     * @param TagRepository $tagRepository Repositorio para gestionar la base de datos de etiquetas.
     */
    public function __construct(
        private readonly TagRepository $tagRepository
    ) {
    }

    /**
     * Muestra la lista de todas las etiquetas pertenecientes al usuario autenticado.
     *
     * @return Response La vista renderizada con la tabla de etiquetas.
     */
    #[Route('/', name: 'app_tag_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('tag/index.html.twig', [
            'tags' => $this->tagRepository->findAllByUser($this->getUser()),
        ]);
    }

    /**
     * Muestra y procesa el formulario para crear una nueva etiqueta.
     *
     * @param Request $request La petición HTTP con los datos del formulario.
     * @return Response Redirección al índice si tiene éxito, o la vista del formulario si no.
     */
    #[Route('/new', name: 'app_tag_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag->setUser($this->getUser());
            $this->tagRepository->save($tag, true);

            $this->addFlash('success', 'Nueva etiqueta creada correctamente.');

            return $this->redirectToRoute('app_tag_index');
        }

        return $this->render('tag/new.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    /**
     * Muestra y procesa el formulario de edición de una etiqueta.
     *
     * @param Request $request La petición HTTP.
     * @param Tag $tag La etiqueta a editar.
     * @return Response Redirección al índice si tiene éxito, o la vista del formulario si no.
     */
    #[Route('/{id}/edit', name: 'app_tag_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tag $tag): Response
    {
        if ($tag->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para editar esta etiqueta.');

            return $this->redirectToRoute('app_tag_index');
        }

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagRepository->save($tag, true);

            $this->addFlash('success', 'Etiqueta actualizada correctamente.');

            return $this->redirectToRoute('app_tag_index');
        }

        return $this->render('tag/edit.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    /**
     * Muestra el detalle de una etiqueta específica.
     *
     * @param Tag $tag La etiqueta solicitada.
     * @return Response La vista de detalle o redirección segura si no hay permisos.
     */
    #[Route('/{id}', name: 'app_tag_show', methods: ['GET'])]
    public function show(Tag $tag): Response
    {
        if ($tag->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para ver esta etiqueta.');

            return $this->redirectToRoute('app_tag_index');
        }

        return $this->render('tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    /**
     * Elimina una etiqueta de la base de datos de forma segura.
     * Valida el token CSRF y la propiedad de la entidad.
     *
     * @param Request $request La petición HTTP para validar el token.
     * @param Tag $tag La etiqueta a eliminar.
     * @return Response Redirección al índice de etiquetas tras la operación.
     */
    #[Route('/{id}', name: 'app_tag_delete', methods: ['POST'])]
    public function delete(Request $request, Tag $tag): Response
    {
        if ($tag->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'No tienes permiso para eliminar esta etiqueta.');

            return $this->redirectToRoute('app_tag_index');
        }

        if ($this->isCsrfTokenValid('delete'.$tag->getId(), (string) $request->request->get('_token'))) {
            $this->tagRepository->remove($tag, true);
            $this->addFlash('success', 'Etiqueta eliminada con éxito.');
        } else {
            $this->addFlash('error', 'Token de seguridad inválido. No se pudo eliminar la etiqueta.');
        }

        return $this->redirectToRoute('app_tag_index');
    }
}
