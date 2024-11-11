<?php

namespace App\Controller;

use App\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/member')]
class MemberController extends AbstractController
{
    #[Route('/', name: 'app_member')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $members = $entityManager->getRepository(Member::class)->findAll();
        
        return $this->render('member/index.html.twig', [
            'members' => $members,
        ]);
    }
    
    #[Route('/{id}', name: 'app_member_show', requirements: ['id' => '\d+'])]
    public function show(Member $member): Response
    {
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
