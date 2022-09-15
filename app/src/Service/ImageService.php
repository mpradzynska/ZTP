<?php

/**
 * Gallery service.
 */

namespace App\Service;

use App\Entity\Gallery;
use App\Entity\Image;
use App\Repository\ImageRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ImageService.
 */
class ImageService implements ImageServiceInterface
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
     * @param Gallery $gallery Gallery entity
     * @param int     $page    Page number
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
     * Save image.
     *
     * @param Image $image Image entity
     */
    public function save(Image $image): void
    {
        $this->imageRepository->save($image, flush: true);
    }

    /**
     * Delete image.
     *
     * @param Image $image Image entity
     */
    public function delete(Image $image): void
    {
        $this->imageRepository->delete($image, flush: true);
    }
}
