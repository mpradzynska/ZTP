<?php

namespace App\Tests\Functional\Comment;

use App\Entity\Comment;
use App\Entity\Enum\UserRole;
use App\Entity\Gallery;
use App\Entity\Image;
use App\Repository\CommentRepository;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class CommentControllerTest extends FunctionalTestCase
{
    private CommentRepository $commentRepository;

    /**
     * @throws ServiceCircularReferenceException When a circular reference is detected
     * @throws ServiceNotFoundException          When the service is not defined
     */
    public function setUp(): void
    {
        parent::setUp();
        $container = static::getContainer();
        $this->commentRepository = $container->get(CommentRepository::class);
    }

    public function testSuccessfullyCreatesComment(): void
    {
        $image = $this->createImage(
            title: 'Title',
            path: 'https://images.com/image.jpg',
            description: 'Description',
        );

        $crawler = $this->makeGetRequest("/comments/create/{$image->getId()}");
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('submit')->form();

        $this->httpClient->submit($form, [
            'comment[email]' => 'user@email.pl',
            'comment[nick]' => 'Nick',
            'comment[text]' => 'New test comment',
        ]);

        $comment = $this->commentRepository->findOneBy(['email' => 'user@email.pl']);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertSame('user@email.pl', $comment->getEmail());
        $this->assertSame('Nick', $comment->getNick());
        $this->assertSame('New test comment', $comment->getText());
        $this->assertSame($image->getId(), $comment->getImage()->getId());
    }

    public function testFailsToCreatesCommentIfImageDoesNotExists(): void
    {
        $this->makeGetRequest("/comments/create/999");
        self::assertResponseStatusCodeSame(404);
    }

    public function testSuccessfullyDeletesCommentIfHasPermission(): void
    {
        $comment = $this->createComment(
            nick: 'Nick',
            email: 'nick@email.com',
            text: 'Comment content',
        );

        $commentId = $comment->getId();

        $this->httpClient->loginUser($this->createUser([UserRole::ROLE_ADMIN]));

        $crawler = $this->makeGetRequest("/comments/delete/$commentId");
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('submit')->form();
        $this->httpClient->submit($form);

        $comment = $this->commentRepository->find($commentId);

        $this->assertNull($comment);
    }

    public function testFailsToDeleteCommentIfNoSufficientPermissions(): void
    {
        $comment = $this->createComment(
            nick: 'Nick',
            email: 'nick@email.com',
            text: 'Comment content',
        );

        $commentId = $comment->getId();

        $this->httpClient->loginUser($this->createUser([UserRole::ROLE_USER]));

        $this->makeGetRequest("/comments/delete/$commentId");
        self::assertResponseStatusCodeSame(403);
    }

    public function testFailsToDeleteCommentIfOneDoesNotExists(): void
    {
        $this->httpClient->loginUser($this->createUser([UserRole::ROLE_ADMIN]));

        $this->makeGetRequest("/comments/delete/999");
        self::assertResponseStatusCodeSame(404);
    }

    private function createGallery(string $name = 'Gallery name'): Gallery
    {
        $gallery = new Gallery();
        $gallery->setUser(
            $this->createAdmin('gallery-creator@email.com')
        );
        $gallery->setName($name);
        $this->em->persist($gallery);
        $this->em->flush();

        return $gallery;
    }

    private function createImage(
        string  $title = 'Image title',
        string  $path = 'https://images.com/image1.jpg',
        string  $description = 'Image description',
        ?Gallery $gallery = null,
    ): Image
    {
        if ($gallery === null) {
            $gallery = $this->createGallery();
        }

        $image = new Image();
        $image->setGallery($gallery);
        $image->setPath($path);
        $image->setTitle($title);
        $image->setDescription($description);
        $this->em->persist($image);
        $this->em->flush();

        return $image;
    }

    private function createComment(
        string $nick = 'AuthorNick',
        string $email = 'default@email.com',
        string $text = 'Default comment content',
        ?Image $image = null,
    ): Comment
    {
        if ($image === null) {
            $image = $this->createImage();
        }

        $comment = new Comment();
        $comment->setImage($image);
        $comment->setEmail($email);
        $comment->setNick($nick);
        $comment->setText($text);

        $this->em->persist($comment);
        $this->em->flush();

        return $comment;
    }
}