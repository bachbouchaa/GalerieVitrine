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
        // Data for painting collections
        $membersData = [
            'nermine@example.com' => [
                'paintings' => [
                    [
                        'title' => 'Mona Lisa',
                        'artist' => 'Leonardo da Vinci',
                        'year' => 1503,
                        'image' => 'mona_lisa.webp'
                    ],
                    [
                        'title' => 'Madonna of the Rocks',
                        'artist' => 'Leonardo da Vinci',
                        'year' => 1483,
                        'image' => 'madonna.jpg'
                    ],
                    [
                        'title' => 'Lady with an Ermine',
                        'artist' => 'Leonardo da Vinci',
                        'year' => 1489,
                        'image' => 'lady.jpg'
                    ],
                    [
                        'title' => 'Vitruvian Man',
                        'artist' => 'Leonardo da Vinci',
                        'year' => 1490,
                        'image' => 'Vitruvian.jpg'
                    ],
                ]
            ],
            'khalil@example.com' => [
                'paintings' => [
                    [
                        'title' => 'The Starry Night',
                        'artist' => 'Vincent van Gogh',
                        'year' => 1889,
                        'image' => 'starry_night.jpg'
                    ],
                    [
                        'title' => 'Sunflowers',
                        'artist' => 'Vincent van Gogh',
                        'year' => 1888,
                        'image' => 'sunflowers.jpg'
                    ],
                    [
                        'title' => 'Wheatfield with Crows',
                        'artist' => 'Vincent van Gogh',
                        'year' => 1890,
                        'image' => 'Wheatfield.jpg'
                    ],
                    [
                        'title' => 'Almond Blossoms',
                        'artist' => 'Vincent van Gogh',
                        'year' => 1890,
                        'image' => 'almond.jpg'
                    ],
                ]
            ]
        ];

        foreach ($membersData as $email => $data) {
            // Retrieve the Member reference
            $member = $this->getReference('member_' . $email);

            // Create a painting collection for each member
            $collection = new MyPaintingCollection();
            $collection->setName("Collection of {$email}")
                ->setDescription("A private collection for {$email}")
                ->setMember($member);
            $manager->persist($collection);

            // Create paintings for the member's collection
            $paintings = [];
            foreach ($data['paintings'] as $paintingInfo) {
                $painting = new Painting();
                $painting->setTitle($paintingInfo['title'])
                    ->setArtist($paintingInfo['artist'])
                    ->setCreationYear($paintingInfo['year'])
                    ->setDescription("A masterpiece by {$paintingInfo['artist']}")
                    ->setMyPaintingCollection($collection)
                    ->setImageName($paintingInfo['image']); // Set the image filename
                $manager->persist($painting);
                $paintings[] = $painting; // Collect paintings to associate with galleries
            }

            // Create galleries and associate paintings
            $gallery1 = new Gallery();
            $gallery1->setDescription("Renaissance Highlights of {$email}")
                ->setPublished(true)
                ->setMember($member);

            // Assign the first two paintings to gallery 1
            foreach (array_slice($paintings, 0, 2) as $painting) {
                $gallery1->addPainting($painting);
            }
            $manager->persist($gallery1);

            $gallery2 = new Gallery();
            $gallery2->setDescription("Special Collection of {$email}")
                ->setPublished(false)
                ->setMember($member);

            // Assign the last two paintings to gallery 2
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
