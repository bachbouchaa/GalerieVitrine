<?php

namespace App\Controller;

use App\Entity\Painting;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaintingController extends AbstractController
{
    #[Route('/painting/{id}', name: 'painting_show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        // Récupération de l'entité `Painting` par son identifiant
        $entityManager = $doctrine->getManager();
        $painting = $entityManager->getRepository(Painting::class)->find($id);

        // Si le painting n'existe pas, nous levons une exception qui renverra une réponse HTTP 404 (Not Found)
        if (!$painting) {
            throw $this->createNotFoundException('The painting does not exist');
        }

        // Rendu du gabarit Twig avec les données du painting
        return $this->render('painting/show.html.twig', [
            'painting' => $painting,
        ]);
    }
}
