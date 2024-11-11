<?php

namespace App\DataFixtures;

use App\Entity\Member;
use App\Entity\MyPaintingCollection;
use App\Entity\Painting;
use App\Entity\Gallery;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        // Member data with realistic painting details
        $membersData = [
            [
                'email' => 'nermine@example.com',
                'password' => '123456',
                'paintings' => [
                    ['title' => 'Mona Lisa', 'artist' => 'Leonardo da Vinci', 'year' => 1503],
                    ['title' => 'Madonna of the Rocks', 'artist' => 'Leonardo da Vinci', 'year' => 1483],
                    ['title' => 'Lady with an Ermine', 'artist' => 'Leonardo da Vinci', 'year' => 1489],
                    ['title' => 'Vitruvian Man', 'artist' => 'Leonardo da Vinci', 'year' => 1490],
                ]
            ],
            [
                'email' => 'khalil@example.com',
                'password' => '123456',
                'paintings' => [
                    ['title' => 'The Starry Night', 'artist' => 'Vincent van Gogh', 'year' => 1889],
                    ['title' => 'Sunflowers', 'artist' => 'Vincent van Gogh', 'year' => 1888],
                    ['title' => 'Wheatfield with Crows', 'artist' => 'Vincent van Gogh', 'year' => 1890],
                    ['title' => 'Almond Blossoms', 'artist' => 'Vincent van Gogh', 'year' => 1890],
                ]
            ]
        ];

        foreach ($membersData as $memberData) {
            // Create Member and hash password
            $member = new Member();
            $member->setEmail($memberData['email']);
            $member->setPassword($this->hasher->hashPassword($member, $memberData['password']));
            $manager->persist($member);

            // Create a unique MyPaintingCollection for each Member
            $collection = new MyPaintingCollection();
            $collection->setName("Collection of {$memberData['email']}")
                       ->setDescription("A private collection for {$memberData['email']}")
                       ->setMember($member);
            $manager->persist($collection);

            // Add Paintings to each Member's MyPaintingCollection
            $paintings = [];
            foreach ($memberData['paintings'] as $paintingInfo) {
                $painting = new Painting();
                $painting->setTitle($paintingInfo['title'])
                         ->setArtist($paintingInfo['artist'])
                         ->setCreationYear($paintingInfo['year'])
                         ->setDescription("A masterpiece by {$paintingInfo['artist']}")
                         ->setMyPaintingCollection($collection);
                $manager->persist($painting);
                $paintings[] = $painting; // Collect paintings for later association with galleries
            }

            // Create Galleries for each Member and associate them with distinct Paintings
            $gallery1 = new Gallery();
            $gallery1->setDescription("Renaissance Highlights of {$memberData['email']}")
                     ->setPublished(true)
                     ->setMember($member);
            
            // Assign the first two paintings to gallery1
            foreach (array_slice($paintings, 0, 2) as $painting) {
                $gallery1->addPainting($painting);
            }
            $manager->persist($gallery1);

            $gallery2 = new Gallery();
            $gallery2->setDescription("Special Collection of {$memberData['email']}")
                     ->setPublished(false)
                     ->setMember($member);

            // Assign the last two paintings to gallery2
            foreach (array_slice($paintings, 2, 2) as $painting) {
                $gallery2->addPainting($painting);
            }
            $manager->persist($gallery2);
        }

        $manager->flush();
    }
}

