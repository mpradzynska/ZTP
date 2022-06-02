<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Repository\GalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/galleries')]
class GalleriesController extends AbstractController
{
    public function __construct(
        private GalleryRepository $repository,
    ) {
    }

    #[Route(
        '/add',
        name: 'gallery_add',
        methods: 'GET'
    )]
    public function addGallery(): Response
    {
        $gallery = new Gallery('gallery1');
        $this->repository->add($gallery, true);
        return $this->render(
            'galleries/add.html.twig',
            ['gallery' => $gallery],
        );
    }
}