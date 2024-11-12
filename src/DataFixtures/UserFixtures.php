<?php

namespace App\DataFixtures;

use App\Entity\Member;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$email, $plainPassword, $role]) {
            // Créez un nouveau Member (équivalent de l'utilisateur dans ce contexte)
            $member = new Member();
            $member->setEmail($email);
            $hashedPassword = $this->hasher->hashPassword($member, $plainPassword);
            $member->setPassword($hashedPassword);
            $member->setRoles([$role]);

            // Enregistrez l'entité Member dans la base de données
            $manager->persist($member);

            // Ajoutez une référence pour l'utiliser dans AppFixtures
            $this->addReference('member_' . $email, $member);
        }

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            ['nermine@example.com', '123456', 'ROLE_USER'],
            ['khalil@example.com', '123456', 'ROLE_ADMIN'],
        ];
    }
}
