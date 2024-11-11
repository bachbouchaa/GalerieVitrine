<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Member;
use App\Entity\Painting;
use App\Form\GalleryType;
use App\Repository\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gallery')]
final class GalleryController extends AbstractController
{
    #[Route(name: 'app_gallery_index', methods: ['GET'])]
    public function index(GalleryRepository $galleryRepository): Response
    {
        // Retrieve only published galleries
        $galleries = $galleryRepository->findBy(['published' => true]);

        return $this->render('gallery/index.html.twig', [
            'galleries' => $galleries,
        ]);
    }

    #[Route('/new/{member_id}', name: 'app_gallery_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[MapEntity(id: 'member_id')] Member $member): Response
    {
        $gallery = new Gallery();
        $gallery->setMember($member); // Associate gallery with the member

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gallery);
            $entityManager->flush();

            // Redirect to the member’s show page after creating the gallery
            return $this->redirectToRoute('app_member_show', [
                'id' => $member->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gallery/new.html.twig', [
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gallery_show', methods: ['GET'])]
    public function show(Gallery $gallery): Response
    {
        return $this->render('gallery/show.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gallery_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Gallery $gallery, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Redirect to the member’s show page after editing the gallery
            return $this->redirectToRoute('app_member_show', [
                'id' => $gallery->getMember()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gallery/edit.html.twig', [
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gallery_delete', methods: ['POST'])]
    public function delete(Request $request, Gallery $gallery, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $gallery->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gallery);
            $entityManager->flush();
        }

        // Redirect to the member’s show page after deleting the gallery
        return $this->redirectToRoute('app_member_show', [
            'id' => $gallery->getMember()->getId(),
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{gallery_id}/painting/{painting_id}', name: 'app_gallery_painting_show', methods: ['GET'])]
    public function paintingShow(
        #[MapEntity(id: 'gallery_id')] Gallery $gallery,
        #[MapEntity(id: 'painting_id')] Painting $painting
    ): Response {
        // Check if the painting belongs to this gallery
        if (!$gallery->getPaintings()->contains($painting)) {
            throw $this->createNotFoundException("Ce tableau n'appartient pas à cette galerie !");
        }

        // Ensure the gallery is public
        if (!$gallery->isPublished()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas accéder à cette ressource !");
        }

        return $this->render('gallery/painting_show.html.twig', [
            'painting' => $painting,
            'gallery' => $gallery
        ]);
    }
}
