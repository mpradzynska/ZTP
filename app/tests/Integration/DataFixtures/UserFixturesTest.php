<?php

namespace Integration\DataFixtures;

use App\DataFixtures\CommentFixtures;
use App\DataFixtures\GalleryFixtures;
use App\DataFixtures\ImageFixtures;
use App\DataFixtures\UserFixtures;
use App\Repository\CommentRepository;
use App\Repository\GalleryRepository;
use App\Repository\ImageRepository;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserFixturesTest extends KernelTestCase
{
    private UserFixtures $userFixtures;
    private GalleryFixtures $galleryFixtures;
    private ImageFixtures $imageFixtures;
    private CommentFixtures $commentFixtures;

    private UserRepository $userRepository;
    private GalleryRepository $galleryRepository;
    private ImageRepository $imageRepository;
    private CommentRepository $commentRepository;

    protected EntityManagerInterface $em;

    public function setUp(): void
    {
        parent::setUp();
        $container = static::getContainer();

        $this->em = $container->get(EntityManagerInterface::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->galleryRepository = $container->get(GalleryRepository::class);
        $this->imageRepository = $container->get(ImageRepository::class);
        $this->commentRepository = $container->get(CommentRepository::class);

        $this->userFixtures = $container->get(UserFixtures::class);
        $this->galleryFixtures = $container->get(GalleryFixtures::class);
        $this->imageFixtures = $container->get(ImageFixtures::class);
        $this->commentFixtures = $container->get(CommentFixtures::class);

        $referenceRepository = new ReferenceRepository($this->em);
        $this->userFixtures->setReferenceRepository($referenceRepository);
        $this->galleryFixtures->setReferenceRepository($referenceRepository);
        $this->imageFixtures->setReferenceRepository($referenceRepository);
        $this->commentFixtures->setReferenceRepository($referenceRepository);
    }

    public function testSuccessfullyCreatesImagesFixtures(): void
    {
        $this->userFixtures->load($this->em);
        $this->galleryFixtures->load($this->em);
        $this->imageFixtures->load($this->em);
        $this->commentFixtures->load($this->em);

        $users = $this->userRepository->findAll();
        $galleries = $this->galleryRepository->findAll();
        $images = $this->imageRepository->findAll();
        $comments = $this->commentRepository->findAll();

        $this->assertCount(13, $users);
        $this->assertCount(10, $galleries);
        $this->assertCount(50, $images);
        $this->assertCount(50, $comments);
    }

}