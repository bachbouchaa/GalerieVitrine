<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;


class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/', name: 'home')]
    public function redirectToLogin(EntityManagerInterface $entityManager): Response
    {
       // Ensure the user is logged in
    if (!$this->getUser()) {
        return $this->redirectToRoute('app_login');
    }

    // Fetch the current user from the database
    $currentUser = $entityManager->getRepository(Member::class)
        ->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

    // Check if the user exists in the database
    if (!$currentUser) {
        throw $this->createNotFoundException('User not found.');
    }

    // Get the user's ID
    $userId = $currentUser->getId();

    // Example: Redirect to the user's profile page
    return $this->redirectToRoute('app_member_show', ['id' => $userId]);
    }


    #[Route('/logout', name: 'app_logout', methods: ['GET', 'POST'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        dump("logout");
        // throw new \Exception('Don\'t forget to activate logout in security.yaml');
        return new Response();
    }
}
