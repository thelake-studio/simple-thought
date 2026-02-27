<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controlador encargado de gestionar el perfil del usuario.
 * Permite visualizar, editar los datos personales y eliminar la cuenta de forma definitiva.
 */
#[Route('/profile')]
#[IsGranted('ROLE_USER')]
final class ProfileController extends AbstractController
{
    /**
     * Constructor para la inyección de dependencias.
     *
     * @param EntityManagerInterface $entityManager Gestor de entidades de Doctrine para persistir cambios.
     * @param Security $security Servicio de seguridad para gestionar el cierre de sesión al borrar la cuenta.
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security
    ) {
    }

    /**
     * Muestra la vista principal del perfil del usuario autenticado.
     *
     * @return Response La vista renderizada con los datos del usuario.
     */
    #[Route('/', name: 'app_profile_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * Muestra y procesa el formulario para editar los datos del perfil (ej. nickname, email).
     *
     * @param Request $request La petición HTTP.
     * @return Response Redirección a la vista del perfil tras actualizar, o renderizado del formulario.
     */
    #[Route('/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Tus datos se han actualizado correctamente.');

            return $this->redirectToRoute('app_profile_index');
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * Gestiona la eliminación definitiva de la cuenta del usuario.
     * Cierra la sesión activa y elimina en cascada todos los datos asociados en la base de datos.
     *
     * @param Request $request La petición HTTP para validar el token CSRF.
     * @return Response Redirección a la página de login tras el borrado, o al perfil si falla la seguridad.
     */
    #[Route('/delete', name: 'app_profile_delete', methods: ['POST'])]
    public function delete(Request $request): Response
    {
        $user = $this->getUser();

        // Verificamos que el objeto usuario es nuestra entidad personalizada
        if (!$user instanceof User) {
            $this->addFlash('error', 'Usuario no válido o sesión expirada.');

            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('delete_account'.$user->getId(), (string) $request->request->get('_token'))) {

            // Cerramos sesión antes de destruir al usuario de la BD
            $this->security->logout(false);

            $this->entityManager->remove($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Tu cuenta y todos tus datos han sido eliminados de forma permanente.');

            return $this->redirectToRoute('app_login');
        }

        $this->addFlash('error', 'Token de seguridad inválido. No se pudo borrar la cuenta.');

        return $this->redirectToRoute('app_profile_index');
    }
}
