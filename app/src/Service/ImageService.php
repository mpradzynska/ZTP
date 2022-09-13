<?php

namespace App\Service;

use App\Entity\Gallery;
use App\Entity\Image;
use App\Repository\ImageRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ImageService
{
    /**
     * Constructor.
     *
     * @param ImageRepository     $imageRepository Task repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(private ImageRepository $imageRepository, private PaginatorInterface $paginator)
    {
    }

    public function getById(int $id): ?Image
    {
        return $this->imageRepository->find($id);
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
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

    public function save(Image $gallery): void
    {
        $this->imageRepository->save($gallery, flush: true);
    }

    public function delete(Image $gallery): void
    {
        $this->imageRepository->delete($gallery, flush: true);
    }
}