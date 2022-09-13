<?php

namespace App\Security\Voter;

use App\Entity\Image;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ImageVoter extends Voter
{
    public const LIST = 'IMAGE_LIST';
    public const EDIT = 'IMAGE_EDIT';
    public const VIEW = 'IMAGE_VIEW';
    public const DELETE = 'IMAGE_DELETE';
    public const CREATE = 'IMAGE_CREATE';

    protected function supports(string $attribute, $subject): bool
    {
        switch ($attribute) {
            case self::LIST:
            case self::CREATE:
                return true;
        }

        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE, self::DELETE])
            && $subject instanceof Image;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        switch ($attribute) {
            case self::LIST:
            case self::VIEW:
                return true;
        }

        /** @var User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
            case self::CREATE:
                return $user->isAdmin();
        }

        return false;
    }
}