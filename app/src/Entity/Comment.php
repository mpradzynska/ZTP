<?php
/**
 * Comment entity.
 */

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Comment.
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: '`comments`')]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    private string $nick;

    #[ORM\Column(type: Types::STRING, length: 1024)]
    private string $text;

    #[ORM\ManyToOne(targetEntity: Image::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private Image $image;

    /**
     * Getter for Id.
     *
     * @return int Id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Getter for email.
     *
     * @return string Email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Getter for nick.
     *
     * @return string Nick
     */
    public function getNick(): string
    {
        return $this->nick;
    }

    /**
     * Getter for text.
     *
     * @return string Comment text
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Getter for image.
     *
     * @return Image Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * Setter for email.
     *
     * @param string $email Email
     *
     * @return void Email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Setter for nick.
     *
     * @param string $nick Nick
     */
    public function setNick(string $nick): void
    {
        $this->nick = $nick;
    }

    /**
     * Setter for text.
     *
     * @param string $text Comment text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * Setter for image.
     *
     * @param Image $image Image entity
     */
    public function setImage(Image $image): void
    {
        $this->image = $image;
    }
}
