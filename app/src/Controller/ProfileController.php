<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
final class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
