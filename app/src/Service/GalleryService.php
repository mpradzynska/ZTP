<?php

namespace App\Service;

use App\Entity\Gallery;
use App\Repository\GalleryRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class GalleryService
{
    /**
     * Constructor.
     *
     * @param GalleryService     $galleryRepository Task repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(private GalleryRepository $galleryRepository, private PaginatorInterface $paginator)
    {
    }

    public function getById(int $id): ?Gallery
    {
        return $this->galleryRepository->find($id);
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->galleryRepository->queryAll(),
            $page,
            GalleryRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    public function add(Gallery $gallery): void
    {
        $this->galleryRepository->add($gallery, flush: true);
    }

    public function delete(Gallery $gallery): void
    {
        $this->galleryRepository->delete($gallery, flush: true);
    }
}