<?php
/**
 * Images controller.
 */

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Image;
use App\Form\Type\ImageType;
use App\Security\Voter\ImageVoter;
use App\Service\ImageServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ImagesController.
 */
#[Route('/images')]
class ImagesController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param ImageServiceInterface $imageService Image service
     * @param TranslatorInterface   $translator   Translator
     */
    public function __construct(private ImageServiceInterface $imageService, private TranslatorInterface $translator)
    {
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Gallery entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create/{id}',
        name: 'image_create',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|POST',
    )]
    public function create(Request $request, Gallery $gallery): Response
    {
        $this->denyAccessUnlessGranted(ImageVoter::CREATE);

        $image = new Image();
        $image->setGallery($gallery);
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageService->save($image);

            $redirectTo = $this->generateUrl('gallery_preview', ['id' => $gallery->getId()]);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirect($redirectTo);
        }

        return $this->render(
            'images/create.html.twig',
            ['form' => $form->createView()],
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Image   $image   Image entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/delete/{id}',
        name: 'image_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|DELETE',
    )]
    public function delete(Request $request, Image $image): Response
    {
        $this->denyAccessUnlessGranted(ImageVoter::DELETE, $image);

        $form = $this->createForm(FormType::class, $image, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('image_delete', ['id' => $image->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageService->delete($image);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            'images/delete.html.twig',
            [
                'form' => $form->createView(),
                'image' => $image,
            ]
        );
    }
}
