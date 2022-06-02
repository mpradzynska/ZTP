<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users')]
class UsersController extends AbstractController
{
    public function __construct(
        private UserRepository $repository,
    ) {
    }

    #[Route(
        '/',
        name: 'users_list',
        methods: 'GET'
    )]
    public function listUsers(): Response
    {
        $this->repository->findAll($user, true);
        return $this->render(
            'users/add.html.twig',
            ['user' => $user],
        );
    }

    #[Route(
        '/add',
        name: 'users_add',
        methods: 'GET'
    )]
    public function addUser(): Response
    {
        $user = new User('name', 'secret');
        $this->repository->add($user, true);
        return $this->render(
            'users/add.html.twig',
            ['user' => $user],
        );
    }
}