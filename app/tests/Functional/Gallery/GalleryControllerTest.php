<?php

namespace App\Tests\Functional\Hello;

use App\Entity\Enum\UserRole;
use App\Entity\Gallery;
use App\Repository\GalleryRepository;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

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
        $gallery = new Gallery();
        $gallery->setName('New gallery');
        $this->em->persist($gallery);
        $this->em->flush();

        $this->httpClient->loginUser($this->createUser([UserRole::ROLE_ADMIN]));
        $this->httpClient->request('GET', '/galleries');


        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('html li', 'New gallery');
    }
}