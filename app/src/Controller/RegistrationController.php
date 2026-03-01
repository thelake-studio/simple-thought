<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controlador encargado de gestionar el registro de nuevos usuarios.
 * Permite la creación de cuentas, hash de contraseñas y auto-login tras el registro exitoso.
 */
final class RegistrationController extends AbstractController
{
    /**
     * Constructor para la inyección de dependencias.
     *
     * @param UserPasswordHasherInterface $userPasswordHasher Servicio para encriptar las contraseñas.
     * @param Security $security Servicio de seguridad para loguear automáticamente al usuario.
     * @param EntityManagerInterface $entityManager Gestor de entidades de Doctrine.
     */
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Muestra y procesa el formulario de registro.
     * Si el registro es válido, persiste el usuario y lo autentica automáticamente.
     *
     * @param Request $request La petición HTTP.
     * @return Response Redirección al panel principal o vista del formulario si hay errores.
     */
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', '¡Bienvenido a Simple Thought! Tu cuenta ha sido creada con éxito.');

            return $this->security->login($user, AppCustomAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
