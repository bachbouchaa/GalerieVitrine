<?php

namespace App\Entity;

use App\Repository\PaintingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaintingRepository::class)]
class Painting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private ?string $artist = null;

    #[ORM\Column]
    #[ORM\JoinColumn(nullable: false)]
    private ?int $creationYear = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $style = null;

    #[ORM\ManyToOne(inversedBy: 'paintings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MyPaintingCollection $myPaintingCollection = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): static
    {
        $this->artist = $artist;

        return $this;
    }

    public function getCreationYear(): ?int
    {
        return $this->creationYear;
    }
    
    public function setCreationYear(int $creationYear): static
    {
        $this->creationYear = $creationYear;
        
        return $this;
    }
    

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function setStyle(?string $style): static
    {
        $this->style = $style;

        return $this;
    }

    public function getMyPaintingCollection(): ?MyPaintingCollection
    {
        return $this->myPaintingCollection;
    }

    public function setMyPaintingCollection(?MyPaintingCollection $myPaintingCollection): static
    {
        $this->myPaintingCollection = $myPaintingCollection;

        return $this;
    }
    public function __toString(): string
    {
        return sprintf(
            'Painting: %s, Artist: %s, Year: %s, Description: %s, Style: %s, Collection: %s',
            $this->title ?? 'Untitled',
            $this->artist ?? 'Unknown Artist',
            $this->creationYear ?? 'Unknown Year',
            $this->description ?? 'No Description',
            $this->style ?? 'Unknown Style',
            $this->myPaintingCollection ? $this->myPaintingCollection->getName() : 'No Collection'
            );
    }
    
}
