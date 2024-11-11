<?php

namespace App\DataFixtures;

use App\Entity\MyPaintingCollection;
use App\Entity\Painting;
use App\Entity\Gallery;
use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    private const RENAISSANCE_COLLECTION = 'renaissance-collection';
    private const MODERN_COLLECTION = 'modern-collection';
    private const IMPRESSIONIST_COLLECTION = 'impressionist-collection';

    private const RENAISSANCE_GALLERY = 'renaissance-gallery';
    private const MODERN_GALLERY = 'modern-gallery';

    private const MEMBER_NERMINE = 'member-nermine';
    private const MEMBER_KHALIL = 'member-khalil';

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    private static function paintingCollectionDataGenerator()
    {
        yield ['Renaissance Collection', 'Collection of Renaissance era paintings', self::RENAISSANCE_COLLECTION];
        yield ['Modern Collection', 'Collection of modern art paintings', self::MODERN_COLLECTION];
        yield ['Impressionist Collection', 'Famous impressionist works', self::IMPRESSIONIST_COLLECTION];
    }

    private static function paintingsDataGenerator()
    {
        yield [self::RENAISSANCE_COLLECTION, 'Mona Lisa', 'Leonardo da Vinci', 1503, 'A portrait painting by Leonardo da Vinci', 'Renaissance'];
        yield [self::MODERN_COLLECTION, 'Starry Night', 'Vincent van Gogh', 1889, 'A depiction of Van Gogh\'s dreamy interpretation of the night sky', 'Post-Impressionism'];
        yield [self::MODERN_COLLECTION, 'The Persistence of Memory', 'Salvador Dalí', 1931, 'Surrealism painting by Salvador Dalí', 'Surrealism'];
        yield [self::IMPRESSIONIST_COLLECTION, 'Impression, Sunrise', 'Claude Monet', 1872, 'Impressionist painting by Claude Monet', 'Impressionism'];
    }

    private static function galleriesDataGenerator()
    {
        yield ['Galerie des maîtres de la Renaissance', true, self::RENAISSANCE_GALLERY, self::MEMBER_NERMINE];
        yield ['Art moderne et contemporain', false, self::MODERN_GALLERY, self::MEMBER_KHALIL];
    }

    private static function membersDataGenerator()
    {
        yield ['nermine@localhost', '123456', self::MEMBER_NERMINE];
        yield ['khalil@localhost', '123456', self::MEMBER_KHALIL];
    }

    public function load(ObjectManager $manager)
    {
        // Load Members
        foreach (self::membersDataGenerator() as [$email, $plainPassword, $memberReference]) {
            $member = new Member();
            $password = $this->hasher->hashPassword($member, $plainPassword);
            $member->setEmail($email);
            $member->setPassword($password);

            $manager->persist($member);
            $this->addReference($memberReference, $member);
        }

        // Load Painting Collections
        foreach (self::paintingCollectionDataGenerator() as [$name, $description, $collectionReference]) {
            $collection = new MyPaintingCollection();
            $collection->setName($name)
                       ->setDescription($description);
            $manager->persist($collection);
            $manager->flush();

            $this->addReference($collectionReference, $collection);
        }

        // Load Paintings
        $paintings = [];
        foreach (self::paintingsDataGenerator() as [$collectionReference, $title, $artist, $creationYear, $description, $style]) {
            $collection = $this->getReference($collectionReference);

            $painting = new Painting();
            $painting->setTitle($title)
                     ->setArtist($artist)
                     ->setCreationYear($creationYear)
                     ->setDescription($description)
                     ->setStyle($style)
                     ->setMyPaintingCollection($collection);

            $manager->persist($painting);
            $paintings[] = $painting;
        }

        // Load Galleries and associate paintings
        foreach (self::galleriesDataGenerator() as [$description, $published, $galleryReference, $memberReference]) {
            $gallery = new Gallery();
            $gallery->setDescription($description)
                    ->setPublished($published);

            // Associate a few paintings with the gallery
            foreach ($paintings as $painting) {
                $gallery->addPainting($painting);
            }

            // Associate the gallery with a member
            $member = $this->getReference($memberReference);
            $gallery->setMember($member);

            $manager->persist($gallery);
            $this->addReference($galleryReference, $gallery);
        }

        $manager->flush();
    }
}
