<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\User;
use App\Form\Type\GalleryType;
use App\Repository\GalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        name: 'gallery_index',
        methods: 'GET'
    )]
    public function index(): Response
    {
        return $this->render(
            'galleries/index.html.twig',
            ['galleries' => $this->repository->findAll()],
        );
    }

    #[Route(
        '/{id}',
        name: 'gallery_preview',
        requirements: ['id' => '\d+'],
        methods: 'GET',
    )]
    public function view(int $id): Response
    {
        $gallery = $this->repository->find($id);

        if ($gallery === null) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'galleries/preview.html.twig',
            ['gallery' => $gallery],
        );
    }

    #[Route(
        '/edit/{id}',
        name: 'gallery_edit',
        requirements: ['id' => '\d+'],
        methods: 'GET|POST',
    )]
    public function edit(Request $request, int $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user === null || $user->isAdmin() === false) {
            throw $this->createAccessDeniedException();
        }

        $gallery = $this->repository->find($id);
        if ($gallery === null) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->add($gallery, true);

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            'galleries/edit.html.twig',
            ['form' => $form->createView()],
        );
    }

    #[Route(
        '/create',
        name: 'gallery_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user === null || $user->isAdmin() === false) {
            throw $this->createAccessDeniedException();
        }
        $gallery = new Gallery();
        $gallery->setUser($user);
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->add($gallery, true);

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            'galleries/create.html.twig',
            ['form' => $form->createView()],
        );
    }
}
