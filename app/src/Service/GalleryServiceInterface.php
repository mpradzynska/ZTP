<?php
/**
 * Gallery service interface.
 */

namespace App\Service;

use App\Entity\Gallery;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface GalleryServiceInterface.
 */
interface GalleryServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save gallery.
     *
     * @param Gallery $gallery Gallery entity
     */
    public function save(Gallery $gallery): void;

    /**
     * Delete gallery.
     *
     * @param Gallery $gallery Gallery entity
     */
    public function delete(Gallery $gallery): void;
}
