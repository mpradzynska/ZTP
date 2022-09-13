<?php
/**
 * Users controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\ChangeUserDataType;
use App\Repository\UserRepository;
use App\Security\Voter\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

// @todo remove this class?

/**
 * Class UsersController.
 */
#[Route('/users')]
class UsersController extends AbstractController
{
    /**
     * @param UserRepository              $userRepository
     * @param UserPasswordHasherInterface $passwordHasher
     * @param TranslatorInterface         $translator
     */
    public function __construct(private UserRepository $userRepository, private UserPasswordHasherInterface $passwordHasher, private TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'list_users',
        methods: 'GET'
    )]
    public function index(): Response
    {
        return $this->render(
            'users/list.html.twig',
            ['users' => $this->userRepository->findAll()],
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
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
            $this->userRepository->save($user, flush: true);

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            'users/edit.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
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
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                $data->getPassword(),
            ));
            $this->userRepository->save($user, flush: true);

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
