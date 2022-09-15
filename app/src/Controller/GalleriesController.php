<?php
/**
 * Galleries controller.
 */

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\User;
use App\Form\Type\GalleryType;
use App\Security\Voter\GalleryVoter;
use App\Service\GalleryServiceInterface;
use App\Service\ImageServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class GalleriesController.
 */
#[Route('/galleries')]
class GalleriesController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param GalleryServiceInterface $galleryService Gallery service
     * @param ImageServiceInterface   $imageService   Image service
     * @param TranslatorInterface     $translator     Translator
     */
    public function __construct(private GalleryServiceInterface $galleryService, private ImageServiceInterface $imageService, private TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'gallery_index',
        methods: 'GET'
    )]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted(GalleryVoter::LIST);

        $page = $request->query->getInt('page', 1);

        return $this->render(
            'galleries/index.html.twig',
            ['pagination' => $this->galleryService->getPaginatedList($page)],
        );
    }

    /**
     * View action.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Gallery entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'gallery_preview',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET',
    )]
    public function view(Request $request, Gallery $gallery): Response
    {
        $this->denyAccessUnlessGranted(GalleryVoter::VIEW, $gallery);

        $page = $request->query->getInt('page', 1);
        $imagesPagination = $this->imageService->getPaginatedList($gallery, $page);

        return $this->render(
            'galleries/preview.html.twig',
            ['gallery' => $gallery, 'imagesPagination' => $imagesPagination],
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Gallery entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/edit/{id}',
        name: 'gallery_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|POST',
    )]
    public function edit(Request $request, Gallery $gallery): Response
    {
        $this->denyAccessUnlessGranted(GalleryVoter::EDIT, $gallery);

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleryService->save($gallery, true);

            $this->addFlash(
                'success',
                $this->translator->trans('message.updated_successfully')
            );

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            'galleries/edit.html.twig',
            ['form' => $form->createView()],
        );
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'gallery_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        /** @var ?User $user */
        $user = $this->getUser();
        if (null === $user || false === $user->isAdmin()) {
            throw new HttpException(403);
        }
        $gallery = new Gallery();
        $gallery->setUser($user);
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleryService->save($gallery);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('gallery_preview', ['id' => $gallery->getId()]);
        }

        return $this->render(
            'galleries/create.html.twig',
            ['form' => $form->createView()],
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Category entity
     *
     * @return Response HTTP response
     */
    #[Route('/delete/{id}', name: 'gallery_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Gallery $gallery): Response
    {
        $this->denyAccessUnlessGranted(GalleryVoter::DELETE, $gallery);

        $form = $this->createForm(FormType::class, $gallery, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('gallery_delete', ['id' => $gallery->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleryService->delete($gallery);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            'galleries/delete.html.twig',
            [
                'form' => $form->createView(),
                'gallery' => $gallery,
            ]
        );
    }
}
