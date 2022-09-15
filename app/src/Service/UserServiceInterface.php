<?php
/**
 * User service interface.
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface UserServiceInterface.
 */
interface UserServiceInterface
{
    /**
     * Save user.
     *
     * @param User $user User entity
     */
    public function save(User $user): void;

    /**
     * Change password.
     *
     * @param User   $user     User entity
     * @param string $password Password
     */
    public function changePassword(User $user, string $password): void;
}
