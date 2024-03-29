<?php
/**
 * Gallery Voter.
 */

namespace App\Security\Voter;

use App\Entity\Gallery;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * GalleryVoter class.
 */
class GalleryVoter extends Voter
{
    public const LIST = 'GALLERY_LIST';
    public const EDIT = 'GALLERY_EDIT';
    public const VIEW = 'GALLERY_VIEW';
    public const DELETE = 'GALLERY_DELETE';
    public const CREATE = 'GALLERY_CREATE';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool Result
     */
    protected function supports(string $attribute, $subject): bool
    {
        switch ($attribute) {
            case self::LIST:
            case self::CREATE:
                return true;
        }

        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE, self::DELETE])
            && $subject instanceof Gallery;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Object
     * @param TokenInterface $token     Security token
     *
     * @return bool Vote result
     */
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
