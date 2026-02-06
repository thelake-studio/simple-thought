<?php

namespace App\Controller;

use App\Repository\EntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
