<?php
/**
 * Comment controller
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Form\Type\CommentType;
use App\Repository\ImageRepository;
use App\Security\Voter\CommentVoter;
use App\Service\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentController
 */
#[Route('/comments')]
class CommentController extends AbstractController
{
    /**
     * @param CommentService $commentService
     * @param ImageRepository   $imageRepository
     */
    public function __construct(private CommentService $commentService, private TranslatorInterface $translator)
    {
    }

    /**
     * @param Request $request
     * @param int     $imageId
     *
     * @return Response
     */
    #[Route(
        '/create/{id}',
        name: 'comment_create',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|POST',
    )]
    public function create(Request $request, Image $image): Response
    {
        $this->denyAccessUnlessGranted(CommentVoter::CREATE);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setImage($image);
            $this->commentService->save($comment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('gallery_preview', ['id' => $image->getGallery()->getId()]);
        }

        return $this->render(
            'comments/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @param Request $request
     * @param int     $imageId
     *
     * @return Response
     */
    #[Route(
        '/list/{id}',
        name: 'comment_list',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET',
    )]
    public function list(Request $request, Image $image): Response
    {
        $this->denyAccessUnlessGranted(CommentVoter::LIST);

        $page = $request->query->getInt('page', 1);

        return $this->render(
            'comments/list.html.twig',
            [
                'image' => $image,
                'comments' => $this->commentService->getPaginatedList($image, $page)
            ],
        );
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    #[Route(
        '/delete/{id}',
        name: 'comment_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|DELETE',
    )]
    public function delete(Request $request, Comment $comment): Response
    {
        $this->denyAccessUnlessGranted(CommentVoter::DELETE, $comment);

        $form = $this->createForm(FormType::class, $comment, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('comment_delete', ['id' => $comment->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->delete($comment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            'comments/delete.html.twig',
            [
                'form' => $form->createView(),
                'comment' => $comment,
            ]
        );
    }
}
