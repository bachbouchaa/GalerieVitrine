<?php

namespace App\DataFixtures;

use App\Entity\MyPaintingCollection;
use App\Entity\Painting;
use App\Entity\Gallery;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Data pour les collections de peintures
        $membersData = [
            'nermine@example.com' => [
                'paintings' => [
                    ['title' => 'Mona Lisa', 'artist' => 'Leonardo da Vinci', 'year' => 1503],
                    ['title' => 'Madonna of the Rocks', 'artist' => 'Leonardo da Vinci', 'year' => 1483],
                    ['title' => 'Lady with an Ermine', 'artist' => 'Leonardo da Vinci', 'year' => 1489],
                    ['title' => 'Vitruvian Man', 'artist' => 'Leonardo da Vinci', 'year' => 1490],
                ]
            ],
            'khalil@example.com' => [
                'paintings' => [
                    ['title' => 'The Starry Night', 'artist' => 'Vincent van Gogh', 'year' => 1889],
                    ['title' => 'Sunflowers', 'artist' => 'Vincent van Gogh', 'year' => 1888],
                    ['title' => 'Wheatfield with Crows', 'artist' => 'Vincent van Gogh', 'year' => 1890],
                    ['title' => 'Almond Blossoms', 'artist' => 'Vincent van Gogh', 'year' => 1890],
                ]
            ]
        ];

        foreach ($membersData as $email => $data) {
            // Récupérer le Member à partir de la référence
            $member = $this->getReference('member_' . $email);

            // Créer une collection de peintures pour chaque membre
            $collection = new MyPaintingCollection();
            $collection->setName("Collection of {$email}")
                ->setDescription("A private collection for {$email}")
                ->setMember($member);
            $manager->persist($collection);

            // Créer des peintures pour la collection du membre
            $paintings = [];
            foreach ($data['paintings'] as $paintingInfo) {
                $painting = new Painting();
                $painting->setTitle($paintingInfo['title'])
                    ->setArtist($paintingInfo['artist'])
                    ->setCreationYear($paintingInfo['year'])
                    ->setDescription("A masterpiece by {$paintingInfo['artist']}")
                    ->setMyPaintingCollection($collection);
                $manager->persist($painting);
                $paintings[] = $painting; // Collecter les peintures pour les associer aux galeries
            }

            // Créer des galeries et y associer les peintures
            $gallery1 = new Gallery();
            $gallery1->setDescription("Renaissance Highlights of {$email}")
                ->setPublished(true)
                ->setMember($member);

            // Assigner les deux premières peintures à la galerie 1
            foreach (array_slice($paintings, 0, 2) as $painting) {
                $gallery1->addPainting($painting);
            }
            $manager->persist($gallery1);

            $gallery2 = new Gallery();
            $gallery2->setDescription("Special Collection of {$email}")
                ->setPublished(false)
                ->setMember($member);

            // Assigner les deux dernières peintures à la galerie 2
            foreach (array_slice($paintings, 2, 2) as $painting) {
                $gallery2->addPainting($painting);
            }
            $manager->persist($gallery2);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
