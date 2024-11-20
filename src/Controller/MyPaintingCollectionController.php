<?php

namespace App\Controller;

use App\Entity\MyPaintingCollection;
use App\Repository\MemberRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyPaintingCollectionController extends AbstractController
{
    #[Route('/my/painting/collection', name: 'app_my_painting_collection')]
    public function index(ManagerRegistry $doctrine, MemberRepository $memberRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login'); // Redirect to login if user is not logged in
        }

        $entityManager = $doctrine->getManager();

        if ($this->isGranted('ROLE_ADMIN')) {
            // Admins can view all collections
            $collections = $entityManager->getRepository(MyPaintingCollection::class)->findAll();
        } else {
            // Regular users can view only their own collections
            $email = $this->getUser()->getUserIdentifier();
            $member = $memberRepository->findOneBy(['email' => $email]);

            if (!$member) {
                throw $this->createNotFoundException("Member not found.");
            }

            $collections = $member->getCollection();
        }

        return $this->render('my_painting_collection/list.html.twig', [
            'collections' => $collections
        ]);
    }


    #[Route('/painting/collection/{id}', name: 'painting_collection_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $collection = $entityManager->getRepository(MyPaintingCollection::class)->find($id);

        if (!$collection) {
            throw $this->createNotFoundException('The collection does not exist');
        }

        // Access control: only the owner or an admin can view the collection
        $hasAccess = $this->isGranted('ROLE_ADMIN') || ($this->getUser() === $collection->getMember());

        if (!$hasAccess) {
            throw $this->createAccessDeniedException("You cannot access this collection!");
        }

        return $this->render('my_painting_collection/show.html.twig', [
            'collection' => $collection,
        ]);
    }
    /*
    #[Route('/', name: 'collection_list', methods: ['GET'])]
    public function listAction(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $collections = $entityManager->getRepository(MyPaintingCollection::class)->findAll();

        return $this->render('my_painting_collection/list.html.twig', [
            'collections' => $collections
        ]);
    }
 */
}
