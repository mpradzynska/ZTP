<?php
/**
 * Users controller
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\ChangeUserDataType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UsersController
 */
#[Route('/users')]
class UsersController extends AbstractController
{
    /**
     * @param UserRepository              $userRepository
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(private UserRepository $userRepository, private UserPasswordHasherInterface $passwordHasher)
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

        $form = $this->createForm(ChangeUserDataType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->add($user, true);

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
            $this->userRepository->add($user, true);

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            'users/edit.html.twig',
            ['form' => $form->createView()]
        );
    }
}
