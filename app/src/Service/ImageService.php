<?php

/**
 * Gallery service
 */

namespace App\Service;

use App\Entity\Gallery;
use App\Entity\Image;
use App\Repository\ImageRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * ImageService class
 */
class ImageService
{
    /**
     * Constructor.
     *
     * @param ImageRepository    $imageRepository Task repository
     * @param PaginatorInterface $paginator       Paginator
     */
    public function __construct(private ImageRepository $imageRepository, private PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param Gallery $gallery
     * @param int     $page
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(Gallery $gallery, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->imageRepository->queryByGallery($gallery),
            $page,
            ImageRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * @param Image $gallery
     *
     * @return void
     */
    public function save(Image $gallery): void
    {
        $this->imageRepository->save($gallery, flush: true);
    }

    /**
     * @param Image $gallery
     *
     * @return void
     */
    public function delete(Image $gallery): void
    {
        $this->imageRepository->delete($gallery, flush: true);
    }
}
