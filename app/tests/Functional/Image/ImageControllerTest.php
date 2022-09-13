<?php

namespace Functional\Image;

use App\Entity\Enum\UserRole;
use App\Entity\Gallery;
use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class ImageControllerTest extends FunctionalTestCase
{
    private ImageRepository $imageRepository;

    /**
     * @throws ServiceCircularReferenceException When a circular reference is detected
     * @throws ServiceNotFoundException          When the service is not defined
     */
    public function setUp(): void
    {
        parent::setUp();
        $container = static::getContainer();
        $this->imageRepository = $container->get(ImageRepository::class);
    }

    public function testSuccessfullyCreatesImage(): void
    {
        $gallery = $this->createGallery(name: 'New name');

        $this->httpClient->loginUser($this->createUser([UserRole::ROLE_ADMIN]));

        $crawler = $this->makeGetRequest("/images/create/{$gallery->getId()}");
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('submit')->form();

        $this->httpClient->submit($form, [
            'image[title]' => 'New image title',
            'image[path]' => 'https://images.com/new-image.jpg',
            'image[description]' => 'New image description',
        ]);

        /** @var Image $image */
        $image = $this->imageRepository->findOneBy(['gallery' => $gallery->getId()]);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertSame('New image title', $image->getTitle());
        $this->assertSame('https://images.com/new-image.jpg', $image->getPath());
        $this->assertSame('New image description', $image->getDescription());
    }

    public function testFailsToCreateImageIfGalleryDoesNotExists(): void
    {
        $this->httpClient->loginUser($this->createUser([UserRole::ROLE_ADMIN]));

        $this->makeGetRequest("/images/create/999");
        self::assertResponseStatusCodeSame(404);
    }

    public function testSuccessfullyDeletesImageIfHasPermission(): void
    {
        $image = $this->createImage();
        $imageId = $image->getId();

        $this->httpClient->loginUser($this->createUser([UserRole::ROLE_ADMIN]));

        $crawler = $this->makeGetRequest("/images/delete/$imageId");
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('submit')->form();
        $this->httpClient->submit($form);

        $image = $this->imageRepository->find($imageId);

        $this->assertNull($image);
    }

    public function testFailsToDeleteImageIfNoSufficientPermissions(): void
    {
        $image = $this->createImage();
        $imageId = $image->getId();

        $this->httpClient->loginUser($this->createUser([UserRole::ROLE_USER]));

        $this->makeGetRequest("/images/delete/$imageId");
        self::assertResponseStatusCodeSame(403);
    }

    public function testFailsToDeleteImageIfOneDoesNotExists(): void
    {
        $this->httpClient->loginUser($this->createUser([UserRole::ROLE_ADMIN]));

        $this->makeGetRequest("/images/delete/999");
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
}