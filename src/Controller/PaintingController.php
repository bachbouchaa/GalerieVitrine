<?php

namespace App\Controller;

use App\Entity\Painting;
use App\Entity\MyPaintingCollection;
use App\Form\PaintingType;
use App\Repository\PaintingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/painting')]
class PaintingController extends AbstractController
{
    #[Route(name: 'app_painting_index', methods: ['GET'])]
    public function index(PaintingRepository $paintingRepository): Response
    {
        return $this->render('painting/index.html.twig', [
            'paintings' => $paintingRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_painting_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MyPaintingCollection $collection): Response
    {
        $painting = new Painting();
        $painting->setMyPaintingCollection($collection);

        $form = $this->createForm(PaintingType::class, $painting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($painting);
            $entityManager->flush();

            // Redirect to the MyPaintingCollection show page after creation
            return $this->redirectToRoute('painting_collection_show', [
                'id' => $collection->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('painting/new.html.twig', [
            'painting' => $painting,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_painting_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Painting $painting, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaintingType::class, $painting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Redirect to the MyPaintingCollection show page after editing
            return $this->redirectToRoute('painting_collection_show', [
                'id' => $painting->getMyPaintingCollection()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('painting/edit.html.twig', [
            'painting' => $painting,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_painting_delete', methods: ['POST'])]
    public function delete(Request $request, Painting $painting, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $painting->getId(), $request->request->get('_token'))) {
            $collectionId = $painting->getMyPaintingCollection()->getId();
            $entityManager->remove($painting);
            $entityManager->flush();

            // Redirect to the MyPaintingCollection show page after deletion
            return $this->redirectToRoute('painting_collection_show', [
                'id' => $collectionId,
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_painting_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_painting_show', methods: ['GET'])]
    public function show(Painting $painting): Response
    {
        return $this->render('painting/show.html.twig', [
            'painting' => $painting,
        ]);
    }
}

