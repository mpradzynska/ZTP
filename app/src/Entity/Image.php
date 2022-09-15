<?php
/**
 * Image entity.
 */

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Image entity.
 */
#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\Table(name: 'images')]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    private string $title;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    private string $description;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\Url]
    private string $path;

    #[ORM\ManyToOne(targetEntity: Gallery::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private Gallery $gallery;

    /**
     * @var Collection<Comment>
     */
    #[ORM\OneToMany(mappedBy: 'image', targetEntity: Comment::class, cascade: ['remove'])]
    private Collection $comments;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
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
     * Getter for title.
     *
     * @return string Image title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Setter for title.
     *
     * @param string $title Image title
     *
     * @return $this Image entity
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Getter for description.
     *
     * @return string Image description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Setter for description.
     *
     * @param string $description Image description
     *
     * @return $this Image entity
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Getter for path.
     *
     * @return string Path to image
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Setter for path.
     *
     * @param string $path Path to image
     *
     * @return $this Image entity
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Getter for gallery.
     *
     * @return Gallery Gallery entity
     */
    public function getGallery(): Gallery
    {
        return $this->gallery;
    }

    /**
     * @param Gallery $gallery Gallery entity
     *
     * @return $this Image entity
     */
    public function setGallery(Gallery $gallery): self
    {
        $this->gallery = $gallery;

        return $this;
    }
}
