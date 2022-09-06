<?php

namespace App\Entity;

use App\Repository\GalleryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

 #[ORM\Entity(repositoryClass: GalleryRepository::class)]
 #[ORM\Table(name: '`galleries`')]
class Gallery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    /**
     * @var Collection<Image>
     */
    #[ORM\OneToMany(mappedBy: 'gallery', targetEntity: Image::class, cascade: ['remove'])]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        $this->images->add($image);

        return $this;
    }
}
