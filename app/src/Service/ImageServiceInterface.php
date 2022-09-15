<?php
/**
 * Image service interface.
 */

namespace App\Service;

use App\Entity\Gallery;
use App\Entity\Image;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface ImageServiceInterface.
 */
interface ImageServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param Gallery $gallery Gallery entity
     * @param int     $page    Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(Gallery $gallery, int $page): PaginationInterface;

    /**
     * Save image.
     *
     * @param Image $image Image entity
     */
    public function save(Image $image): void;

    /**
     * Delete image.
     *
     * @param Image $image Image entity
     */
    public function delete(Image $image): void;
}
