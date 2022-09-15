<?php
/**
 * Users controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\ChangeUserDataType;
use App\Security\Voter\UserVoter;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UsersController.
 */
#[Route('/users')]
class UsersController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private UserServiceInterface $userService, private TranslatorInterface $translator)
    {
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/edit',
        name: 'edit_user',
        methods: 'GET|POST'
    )]
    public function editData(Request $request): Response
    {
        /** @var ?User $user */
        $user = $this->getUser();
        if (null === $user || false === $user->isAdmin()) {
            throw new HttpException(403);
        }

        $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

        $form = $this->createForm(ChangeUserDataType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            'users/edit.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Change-password action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/change-password',
        name: 'change_password',
        methods: 'GET|POST'
    )]
    public function changePassword(Request $request): Response
    {
        /** @var ?User $user */
        $user = $this->getUser();
        if (null === $user || false === $user->isAdmin()) {
            throw new HttpException(403);
        }

        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $data */
            $data = $form->getData();
            $this->userService->changePassword($user, password: $data->getPassword());

            $this->addFlash(
                'success',
                $this->translator->trans('message.password_changed_successfully')
            );

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            'users/edit.html.twig',
            ['form' => $form->createView()]
        );
    }
}
