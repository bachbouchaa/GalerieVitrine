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
use Symfony\Component\HttpFoundation\RedirectResponse;


#[Route('/gallery')]

final class GalleryController extends AbstractController
{
    #[Route('/', name: 'app_gallery_index', methods: ['GET'])]
public function index(GalleryRepository $galleryRepository): Response
{
    $member = $this->getUser();

    if (!$member) {
        // Redirect unauthenticated users to the login page
        return new RedirectResponse($this->generateUrl('app_login'));
    }

    // Retrieve all published galleries
    $publicGalleries = $galleryRepository->findBy(['published' => true]);

    // Retrieve the authenticated user's unpublished galleries
    $privateGalleries = $galleryRepository->findBy([
        'published' => false,
        'member' => $member,
    ]);

    return $this->render('gallery/index.html.twig', [
        'public_galleries' => $publicGalleries,
        'private_galleries' => $privateGalleries,
    ]);
}


    #[Route('/new/{member_id}', name: 'app_gallery_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[MapEntity(id: 'member_id')] Member $member): Response
    {
        // Ensure the user creating a gallery is either the member or an admin
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $member) {
            throw $this->createAccessDeniedException("You do not have permission to create a gallery for this member.");
        }

        $gallery = new Gallery();
        $gallery->setMember($member);

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gallery);
            $entityManager->flush();

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
        $hasAccess = false;

        // Grant access if the user is an admin or the gallery is published
        if ($this->isGranted('ROLE_ADMIN') || $gallery->isPublished()) {
            $hasAccess = true;
        } else {
            // Otherwise, check if the user is the owner of the gallery
            $member = $this->getUser();
            if ($member && ($member === $gallery->getMember())) {
                $hasAccess = true;
            }
        }

        // If access is not granted, throw an access denied exception
        if (!$hasAccess) {
            throw $this->createAccessDeniedException("You cannot access the requested resource!");
        }

        return $this->render('gallery/show.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gallery_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Gallery $gallery, EntityManagerInterface $entityManager): Response
    {
        // Allow only admins or the gallery owner to edit the gallery
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $gallery->getMember()) {
            throw $this->createAccessDeniedException("You do not have permission to edit this gallery.");
        }

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

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
        // Allow only admins or the gallery owner to delete the gallery
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $gallery->getMember()) {
            throw $this->createAccessDeniedException("You do not have permission to delete this gallery.");
        }

        if ($this->isCsrfTokenValid('delete' . $gallery->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gallery);
            $entityManager->flush();
        }

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
            throw $this->createNotFoundException("This painting is not part of this gallery!");
        }

        $hasAccess = false;

        // Allow access if the user is an admin or if the gallery is published
        if ($this->isGranted('ROLE_ADMIN') || $gallery->isPublished()) {
            $hasAccess = true;
        } else {
            // Otherwise, check if the current user is the owner of the gallery
            $member = $this->getUser();
            if ($member && ($member === $gallery->getMember())) {
                $hasAccess = true;
            }
        }

        // If access is not granted, throw an access denied exception
        if (!$hasAccess) {
            throw $this->createAccessDeniedException("You cannot access the requested resource!");
        }

        return $this->render('gallery/painting_show.html.twig', [
            'painting' => $painting,
            'gallery' => $gallery,
        ]);
    }
}
