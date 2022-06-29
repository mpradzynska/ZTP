<?php

namespace App\Entity\Enum;

enum UserRole: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMIN = 'ROLE_ADMIN';
    /**
     * Get the role label.
     *
     * @return string Role label
     */
    public function label(): string
    {
        return match ($this) {
            self::ROLE_USER => 'label.role_user',
            self::ROLE_ADMIN => 'label.role_admin',
        };
    }
}
