<?php

namespace App\Tests\Functional\Gallery;

use App\Entity\Enum\UserRole;
use App\Entity\Gallery;
use App\Entity\Image;
use App\Repository\GalleryRepository;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\Request;

class GalleryControllerTest extends FunctionalTestCase
{
    private GalleryRepository $galleryRepository;

    /**
     * @throws ServiceCircularReferenceException When a circular reference is detected
     * @throws ServiceNotFoundException          When the service is not defined
     */
    public function setUp(): void
    {
        parent::setUp();
        $container = static::getContainer();
        $this->galleryRepository = $container->get(GalleryRepository::class);
    }

    public function testGalleriesCorrectlyListed(): void
    {
        $this->createGallery(name: 'New gallery');

        $this->makeGetRequest('/galleries');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html li', 'New gallery');
    }

    public function testSuccessfullyCreatesGalleryIfHavePermission(): void
    {
        $user = $this->createUser([UserRole::ROLE_ADMIN]);
        $this->httpClient->loginUser($user);

        $crawler = $this->makeGetRequest('/galleries/create');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('submit')->form();
        $this->httpClient->submit($form, [
            'gallery[name]' => 'New test gallery',
        ]);

        $gallery = $this->galleryRepository->findOneBy(['name' => 'New test gallery']);

        $this->assertInstanceOf(Gallery::class, $gallery);
        $this->assertSame('New test gallery', $gallery->getName());
    }

    public function testSuccessfullyDeletesGalleryIfHavePermission(): void
    {
        $gallery = $this->createGallery(name: 'New gallery');
        $galleryId = $gallery->getId();

        $this->httpClient->loginUser($this->createUser([UserRole::ROLE_ADMIN]));

        $this->makeGetRequest("/galleries/delete/$galleryId");
        self::assertResponseRedirects();

        $gallery = $this->galleryRepository->find($galleryId);

        $this->assertNull($gallery);
    }

    public function testSuccessfullyPreviewsGallery(): void
    {
        $gallery = $this->createGallery(name: 'New gallery');
        $this->createImage($gallery, title: 'Image title', path: 'https://link.to/image.jpg', description: 'Descr');
        $galleryId = $gallery->getId();

        $this->httpClient->loginUser($this->createUser([UserRole::ROLE_ADMIN]));

        $this->makeGetRequest("/galleries/$galleryId");
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html title', 'New gallery');
        self::assertSelectorTextContains('html body h1', 'New gallery');
        self::assertSelectorTextContains('html body .image_preview', 'Image title');
    }

    public function testSuccessfullyEditsGalleryIfHavePermission(): void
    {
        $gallery = $this->createGallery(name: 'New gallery');
        $galleryId = $gallery->getId();

        $this->httpClient->loginUser($this->createUser([UserRole::ROLE_ADMIN]));

        $crawler = $this->makeGetRequest("/galleries/edit/$galleryId");
        self::assertResponseIsSuccessful();
        $form = $crawler->selectButton('submit')->form();

        $this->httpClient->submit($form, [
            'gallery[name]' => 'Updated gallery name',
        ]);
        self::assertResponseRedirects();

        $gallery = $this->galleryRepository->find($galleryId);

        $this->assertInstanceOf(Gallery::class, $gallery);
        $this->assertSame('Updated gallery name', $gallery->getName());
    }

    private function createGallery(string $name): Gallery
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
        Gallery $gallery,
        string  $title,
        string  $path,
        string  $description,
    ): Image
    {
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