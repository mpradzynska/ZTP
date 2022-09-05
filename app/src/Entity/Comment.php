<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getNick(): string
    {
        return $this->nick;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setNick(string $nick): void
    {
        $this->nick = $nick;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function setImage(Image $image): void
    {
        $this->image = $image;
    }
}
