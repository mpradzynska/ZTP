<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Image;
use App\Entity\User;
use App\Form\Type\ImageType;
use App\Repository\GalleryRepository;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/images')]
class ImageController extends AbstractController
{
    public function __construct(
        private GalleryRepository $galleryRepository,
        private ImageRepository $imageRepository,
    ) {
    }

    #[Route(
        '/create/{galleryId}',
        name: 'image_create',
        requirements: ['galleryId' => '\d+'],
        methods: 'GET|POST',
    )]
    public function create(Request $request, int $galleryId): Response
    {
        /** @var User $user */
        $user = $request->getUser();
        if (null === $user || false === $user->isAdmin()) {
            throw $this->createAccessDeniedException();
        }

        /** @var Gallery $gallery */
        $gallery = $this->galleryRepository->get($galleryId);
        if (null === $gallery) {
            throw $this->createNotFoundException();
        }

        $image = new Image();
        $image->setGallery($gallery);
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageRepository->add($image, flush: true);

            return $this->redirectToRoute('gallery_preview', ['id' => $gallery->getId()]);
        }

        return $this->render(
            'galleries/create.html.twig',
            ['form' => $form->createView()],
        );
    }

    #[Route(
        '/delete/{id}',
        name: 'image_delete',
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

        $image = $this->imageRepository->find($id);

        if (null === $image) {
            throw $this->createNotFoundException();
        }

        $this->imageRepository->remove($image, flush: true);

        $redirectTo = null; // $this->getReferrer($request);
        if (null === $redirectTo) {
            $galleryId = $image->getGallery()->getId();
            $redirectTo = $this->generateUrl('gallery_preview', ['id' => $galleryId]);
        }

        return $this->redirect($redirectTo);
    }

    private function getReferrer(Request $request): string|null
    {
        return $request->headers->get('referer');
    }
}
