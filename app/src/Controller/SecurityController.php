<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controlador encargado de gestionar la autenticación del usuario.
 * Procesa el inicio de sesión (Login) y el cierre de sesión (Logout).
 */
final class SecurityController extends AbstractController
{
    /**
     * Constructor para la inyección de dependencias.
     *
     * @param AuthenticationUtils $authenticationUtils Utilidad de Symfony para capturar errores y datos de login.
     */
    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils
    ) {
    }

    /**
     * Muestra y procesa el formulario de inicio de sesión.
     * Si el usuario ya está autenticado, lo redirige a la página principal.
     *
     * @return Response La vista renderizada del formulario o redirección si ya hay sesión.
     */
    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(): Response
    {
        // Si el usuario ya está logueado, lo redirigimos para que no vea el formulario
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Gestiona el cierre de sesión del usuario.
     * Este método es interceptado automáticamente por el firewall de Symfony.
     *
     * @return void
     * @throws \LogicException Siempre lanza excepción si el firewall no lo intercepta.
     */
    #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new \LogicException('Este método puede estar en blanco. Será interceptado por la clave "logout" en el firewall (security.yaml).');
    }
}
