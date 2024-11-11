<?php

namespace App\Entity;

use App\Repository\MyPaintingCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MyPaintingCollectionRepository::class)]
class MyPaintingCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Painting::class, mappedBy: 'myPaintingCollection', orphanRemoval: true)]
    private Collection $paintings;

    #[ORM\OneToOne(inversedBy: 'collection', cascade: ['persist', 'remove'])]
    private ?Member $member = null;

    public function __construct()
    {
        $this->paintings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPaintings(): Collection
    {
        return $this->paintings;
    }

    public function addPainting(Painting $painting): static
    {
        if (!$this->paintings->contains($painting)) {
            $this->paintings->add($painting);
            $painting->setMyPaintingCollection($this);
        }

        return $this;
    }

    public function removePainting(Painting $painting): static
    {
        if ($this->paintings->removeElement($painting)) {
            if ($painting->getMyPaintingCollection() === $this) {
                $painting->setMyPaintingCollection(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            'Collection: %s, Description: %s, Number of Paintings: %d',
            $this->name ?? 'Unnamed Collection',
            $this->description ?? 'No Description',
            $this->paintings->count()
        );
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;

        return $this;
    }
}
