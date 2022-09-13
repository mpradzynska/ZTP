<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Image;
use App\Repository\CommentRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class CommentService
{
    /**
     * Task repository.
     */
    private CommentRepository $commentRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param CommentRepository     $commentRepository Task repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(CommentRepository $commentRepository, PaginatorInterface $paginator)
    {
        $this->commentRepository = $commentRepository;
        $this->paginator = $paginator;
    }

    public function getById(int $id): ?Comment
    {
        return $this->commentRepository->find($id);
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(Image $image, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->commentRepository->queryByImage($image),
            $page,
            CommentRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    public function save(Comment $comment): void
    {
        $this->commentRepository->save($comment, flush: true);
    }

    public function delete(Comment $gallery): void
    {
        $this->commentRepository->delete($gallery, flush: true);
    }
}