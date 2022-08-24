<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\Type\CommentType;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comments')]
class CommentController extends AbstractController
{
    public function __construct(
        private CommentRepository $commentRepository,
        private ImageRepository $imageRepository,
    ) {
    }

    #[Route(
        '/create/{imageId}',
        name: 'comment_create',
        requirements: ['imageId' => '\d+'],
        methods: 'GET|POST',
    )]
    public function create(Request $request, int $imageId): Response
    {
        $image = $this->imageRepository->find($imageId);
        if (null === $image) {
            throw $this->createNotFoundException();
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setImage($image);
            $this->commentRepository->add($comment, flush: true);

            return $this->redirectToRoute('gallery_preview', ['id' => $image->getGallery()->getId()]);
        }

        return $this->render(
            'comments/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    #[Route(
        '/delete/{id}',
        name: 'comment_delete',
        requirements: ['id' => '\d+'],
        methods: 'GET',
    )]
    public function delete(Request $request, int $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (null === $user || false === $user->isAdmin()) {
            throw new HttpException(403);
        }

        $comment = $this->commentRepository->find($id);

        if (null === $comment) {
            throw $this->createNotFoundException();
        }

        $this->commentRepository->remove($comment, flush: true);

        $redirectTo = $this->getReferrer($request);
        if (null === $redirectTo) {
            $galleryId = $comment->getImage()->getGallery()->getId();
            $redirectTo = $this->generateUrl('gallery_preview', ['id' => $galleryId]);
        }

        return $this->redirect($redirectTo);
    }

    private function getReferrer(Request $request): string|null
    {
        return $request->headers->get('referer');
    }
}
