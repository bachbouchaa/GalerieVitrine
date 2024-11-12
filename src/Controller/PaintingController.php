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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\MemberRepository;


#[Route('/painting')]
#[IsGranted('ROLE_USER')] // Ensure all routes require at least ROLE_USER
class PaintingController extends AbstractController
{
    #[Route(name: 'app_painting_index', methods: ['GET'])]
    public function index(PaintingRepository $paintingRepository, MemberRepository $memberRepository): Response
    {
        $user = $memberRepository->findOneBy(["email" => $this->getUser()->getUserIdentifier()]);

        if ($this->isGranted('ROLE_ADMIN')) {
            // Admins can view all paintings
            $paintings = $paintingRepository->findAll();
        } else {
            // Regular users can only view their own paintings
            $paintings = $paintingRepository->findBy(['myPaintingCollection' => $user->getCollection()]);
        }

        return $this->render('painting/index.html.twig', [
            'paintings' => $paintings,
        ]);
    }

    #[Route('/new/{id}', name: 'app_painting_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MyPaintingCollection $collection): Response
    {
        // Ensure the collection belongs to the current user, unless they are an admin
        if (!$this->isGranted('ROLE_ADMIN') && $collection->getMember() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not have permission to add paintings to this collection.');
        }

        $painting = new Painting();
        $painting->setMyPaintingCollection($collection);

        $form = $this->createForm(PaintingType::class, $painting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($painting);
            $entityManager->flush();

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
        // Allow only admins or the owner of the painting to edit it
        if (!$this->isGranted('ROLE_ADMIN') && $painting->getMyPaintingCollection()->getMember() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not have permission to edit this painting.');
        }

        $form = $this->createForm(PaintingType::class, $painting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

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
        // Allow only admins or the owner of the painting to delete it
        if (!$this->isGranted('ROLE_ADMIN') && $painting->getMyPaintingCollection()->getMember() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not have permission to delete this painting.');
        }

        if ($this->isCsrfTokenValid('delete' . $painting->getId(), $request->request->get('_token'))) {
            $collectionId = $painting->getMyPaintingCollection()->getId();
            $entityManager->remove($painting);
            $entityManager->flush();

            return $this->redirectToRoute('painting_collection_show', [
                'id' => $collectionId,
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_painting_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_painting_show', methods: ['GET'])]
    public function show(Painting $painting): Response
    {
        // Ensure the user can only view their own paintings, unless they are an admin
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $painting->getMyPaintingCollection()->getMember()) {
            throw $this->createAccessDeniedException('You do not have permission to view this painting.');
        }

        return $this->render('painting/show.html.twig', [
            'painting' => $painting,
        ]);
    }
}
