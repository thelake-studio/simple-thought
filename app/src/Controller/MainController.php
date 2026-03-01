<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controlador principal de la aplicación.
 * Encargado de gestionar la página de inicio pública (Landing Page).
 */
final class MainController extends AbstractController
{
    /**
     * Muestra la página principal de bienvenida de la aplicación.
     *
     * @return Response La vista renderizada de la página de inicio.
     */
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }
}
