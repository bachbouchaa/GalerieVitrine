<?php

namespace App\Controller;

use App\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/member')]
class MemberController extends AbstractController
{
    #[Route('/', name: 'app_member')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Restrict access to admins only
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à accéder à la liste des membres.');
        }

        $members = $entityManager->getRepository(Member::class)->findAll();

        return $this->render('member/index.html.twig', [
            'members' => $members,
        ]);
    }

    #[Route('/{id}', name: 'app_member_show', requirements: ['id' => '\d+'])]
    public function show(Member $member, EntityManagerInterface $entityManager): Response
    {
        // Redirect to login if the user is not authenticated
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Retrieve the current logged-in user
        $currentUser = $entityManager->getRepository(Member::class)
            ->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

        // Restrict access to admins or the member accessing their own profile
        if (!$this->isGranted('ROLE_ADMIN') && $currentUser->getId() !== $member->getId()) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à accéder à cette page.');
        }

        // Retrieve the member's MyPaintingCollection
        $myPaintingCollection = $member->getCollection();

        // Retrieve the member's galleries
        $galleries = $member->getGalleries();

        return $this->render('member/show.html.twig', [
            'member' => $member,
            'myPaintingCollection' => $myPaintingCollection,
            'galleries' => $galleries,
        ]);
    }
}
