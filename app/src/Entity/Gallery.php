<?php
/**
 * Gallery entity.
 */

namespace App\Entity;

use App\Repository\GalleryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Gallery.
 */
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

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for name.
     *
     * @return string Name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Setter for name.
     *
     * @param string $name Name
     *
     * @return $this Gallery entity
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Getter for user.
     *
     * @return User User entity
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Setter for user.
     *
     * @param User $user User entity
     *
     * @return $this Gallery entity
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Getter for images.
     *
     * @return Collection<Image> Images collection
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * Add image to collection.
     *
     * @param Image $image Image entity
     *
     * @return $this Gallery entity
     */
    public function addImage(Image $image): self
    {
        $this->images->add($image);

        return $this;
    }
}
