<?php

namespace App\Controller;

use App\Entity\MyPaintingCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyPaintingCollectionController extends AbstractController
{
    #[Route('/my/painting/collection', name: 'app_my_painting_collection')]
    public function index(): Response
    {
        // Lorsque tout se passe bien, nous renvoyons une réponse HTTP avec le contenu approprié.
        return $this->render('my_painting_collection/index.html.twig', [
            'controller_name' => 'MyPaintingCollectionController',
        ]);
    }

    /**
     * Lists all MyPaintingCollection entities.
     */
    #[Route('/', name: 'collection_list', methods: ['GET'])]   
    public function listAction(ManagerRegistry $doctrine): Response
    {
        // Récupération de toutes les collections de la base de données
        $entityManager = $doctrine->getManager();
        $collections = $entityManager->getRepository(MyPaintingCollection::class)->findAll();
        
        // Utilisation du gabarit Twig pour afficher les collections
        return $this->render('my_painting_collection/list.html.twig', [
            'collections' => $collections
        ]);
    }

       

    /**
     * Shows the details of a single MyPaintingCollection entity.
     */
    #[Route('/painting/collection/{id}', name: 'painting_collection_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $collection = $entityManager->getRepository(MyPaintingCollection::class)->find($id);
        
        if (!$collection) {
            throw $this->createNotFoundException('The collection does not exist');
        }
        
        return $this->render('my_painting_collection/show.html.twig', [
            'collection' => $collection,
        ]);
    }
    
}

