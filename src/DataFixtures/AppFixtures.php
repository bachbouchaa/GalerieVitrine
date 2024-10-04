<?php

namespace App\DataFixtures;

use App\Entity\MyPaintingCollection;
use App\Entity\Painting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    // Defines reference names for instances of MyPaintingCollection
    private const RENAISSANCE_COLLECTION = 'renaissance-collection';
    private const MODERN_COLLECTION = 'modern-collection';
    private const IMPRESSIONIST_COLLECTION = 'impressionist-collection';

    /**
     * Generates initialization data for painting collections: [name, description, collection reference]
     * @return \Generator
     */
    private static function paintingCollectionDataGenerator()
    {
        yield ['Renaissance Collection', 'Collection of Renaissance era paintings', self::RENAISSANCE_COLLECTION];
        yield ['Modern Collection', 'Collection of modern art paintings', self::MODERN_COLLECTION];
        yield ['Impressionist Collection', 'Famous impressionist works', self::IMPRESSIONIST_COLLECTION];
    }

    /**
     * Generates initialization data for paintings: [collection reference, title, artist, creationYear, description, style]
     * @return \Generator
     */
    private static function paintingsDataGenerator()
    {
        yield [self::RENAISSANCE_COLLECTION, 'Mona Lisa', 'Leonardo da Vinci', 1503, 'A portrait painting by Leonardo da Vinci', 'Renaissance'];
        yield [self::MODERN_COLLECTION, 'Starry Night', 'Vincent van Gogh', 1889, 'A depiction of Van Gogh\'s dreamy interpretation of the night sky', 'Post-Impressionism'];
        yield [self::MODERN_COLLECTION, 'The Persistence of Memory', 'Salvador Dalí', 1931, 'Surrealism painting by Salvador Dalí', 'Surrealism'];
        yield [self::IMPRESSIONIST_COLLECTION, 'Impression, Sunrise', 'Claude Monet', 1872, 'Impressionist painting by Claude Monet', 'Impressionism'];
    }

    public function load(ObjectManager $manager)
    {
        // Load Painting Collections
        foreach (self::paintingCollectionDataGenerator() as [$name, $description, $collectionReference]) {
            $collection = new MyPaintingCollection();
            $collection->setName($name)
                       ->setDescription($description);
            $manager->persist($collection);
            $manager->flush();

            // Store reference for later use
            $this->addReference($collectionReference, $collection);
        }

        // Load Paintings
        foreach (self::paintingsDataGenerator() as [$collectionReference, $title, $artist, $creationYear, $description, $style]) {
            // Retrieve the associated collection by reference
            $collection = $this->getReference($collectionReference);

            $painting = new Painting();
            $painting->setTitle($title)
                     ->setArtist($artist)
                     ->setCreationYear($creationYear)
                     ->setDescription($description)
                     ->setStyle($style)
                     ->setMyPaintingCollection($collection);

            // Persist the Painting
            $manager->persist($painting);
        }

        $manager->flush();
    }
}


