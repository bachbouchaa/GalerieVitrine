<?php

namespace App\Controller;

use App\Entity\MyPaintingCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MyPaintingCollectionController extends AbstractController
{
    #[Route('/my/painting/collection', name: 'app_my_painting_collection')]
    public function index(): Response
    {
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
        $htmlpage = '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Painting Collections List</title>
    </head>
    <body>
        <h1>Painting Collections List Of All Members </h1>
        <p>Here are all your collections:</p>
        <ul>';

        // Get the entity manager and fetch all collections
        $entityManager = $doctrine->getManager();
        $collections = $entityManager->getRepository(MyPaintingCollection::class)->findAll();

        foreach ($collections as $collection) {
            // Generate URL for each collection's details (assuming a route named 'collection_show' exists)
            $url = $this->generateUrl('collection_show', ['id' => $collection->getId()]);
            $htmlpage .= '<li><a href="' . $url . '">' . htmlspecialchars($collection->getName()) . '</a></li>';
        }

        $htmlpage .= '</ul>
    </body>
</html>';

        return new Response(
            $htmlpage,
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    /**
     * Lists a MyPaintingCollection entitie.
     */
    #[Route('/collections/{id}', name: 'collection_show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $collection = $entityManager->getRepository(MyPaintingCollection::class)->find($id);

        if (!$collection) {
            throw $this->createNotFoundException('The collection does not exist');
        }

        $htmlpage = '<!DOCTYPE html>
    <html>
 <head>
     <meta charset="UTF-8">
     <title>Collection Details</title>
 </head>
 <body>
     <h1>Collection: ' . htmlspecialchars($collection->getName()) . '</h1>
     <p>Description: ' . htmlspecialchars($collection->getDescription()) . '</p>
     <a href="' . $this->generateUrl('collection_list') . '">Back to list</a>
 </body>
</html>';

        return new Response(
            $htmlpage,
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }
}
