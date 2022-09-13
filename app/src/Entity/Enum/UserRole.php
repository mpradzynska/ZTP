<?php
/**
 * User role
 */

namespace App\Entity\Enum;

/**
 * Enum UserRole
 */
enum UserRole: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMIN = 'ROLE_ADMIN';
}
