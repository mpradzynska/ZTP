<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\User;
use App\Form\Type\GalleryType;
use App\Repository\GalleryRepository;
use App\Repository\ImageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/galleries')]
class GalleriesController extends AbstractController
{
    private const PAGINATION_IMAGES_ITEMS = 5;

    public function __construct(
        private GalleryRepository $galleryRepository,
        private ImageRepository $imageRepository,
        private PaginatorInterface $paginator,
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
            ['galleries' => $this->galleryRepository->findAll()],
        );
    }

    #[Route(
        '/{id}',
        name: 'gallery_preview',
        requirements: ['id' => '\d+'],
        methods: 'GET',
    )]
    public function view(Request $request, int $id): Response
    {
        $gallery = $this->galleryRepository->find($id);

        if (null === $gallery) {
            throw $this->createNotFoundException();
        }

        $imagesPagination = $this->paginator->paginate(
            $this->imageRepository->queryByGallery($gallery),
            $request->query->getInt('page', 1),
            self::PAGINATION_IMAGES_ITEMS,
        );
//        $this->imageRepository

        return $this->render(
            'galleries/preview.html.twig',
            ['gallery' => $gallery, 'imagesPagination' => $imagesPagination],
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
        if (null === $user || false === $user->isAdmin()) {
            throw $this->createAccessDeniedException();
        }

        $gallery = $this->galleryRepository->find($id);
        if (null === $gallery) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleryRepository->add($gallery, true);

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
        if (null === $user || false === $user->isAdmin()) {
            throw $this->createAccessDeniedException();
        }
        $gallery = new Gallery();
        $gallery->setUser($user);
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleryRepository->add($gallery, true);

            return $this->redirectToRoute('gallery_preview', ['id' => $gallery->getId()]);
        }

        return $this->render(
            'galleries/create.html.twig',
            ['form' => $form->createView()],
        );
    }

    #[Route(
        '/delete/{id}',
        name: 'gallery_delete',
        requirements: ['id' => '\d+'],
        methods: 'GET',
    )]
    public function delete(Request $request, int $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (null === $user || false === $user->isAdmin()) {
            throw $this->createAccessDeniedException();
        }

        $gallery = $this->galleryRepository->find($id);

        if (null === $gallery) {
            throw $this->createNotFoundException();
        }

        $this->galleryRepository->remove($gallery, flush: true);

        return $this->redirectToRoute('gallery_index');
    }
}
