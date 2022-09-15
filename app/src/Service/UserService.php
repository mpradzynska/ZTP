<?php
/**
 * User service class.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    /**
     * Constructor.
     *
     * @param UserRepository              $userRepository User repository
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     */
    public function __construct(private UserRepository $userRepository, private UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Save user.
     *
     * @param User $user User entity
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user, flush: true);
    }

    /**
     * Change password.
     *
     * @param User   $user     User entity
     * @param string $password Password
     */
    public function changePassword(User $user, string $password): void
    {
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $password,
        ));
        $this->userRepository->save($user, flush: true);
    }
}
