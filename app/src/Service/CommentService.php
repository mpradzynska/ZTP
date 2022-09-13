<?php
/**
 * Comment service
 */
namespace App\Service;

use App\Entity\Comment;
use App\Entity\Image;
use App\Repository\CommentRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * CommentService class
 */
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
     * @param CommentRepository  $commentRepository Task repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(CommentRepository $commentRepository, PaginatorInterface $paginator)
    {
        $this->commentRepository = $commentRepository;
        $this->paginator = $paginator;
    }

    /**
     * Get paginated list.
     *
     * @param Image $image
     * @param int   $page
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

    /**
     * @param Comment $comment
     *
     * @return void
     */
    public function save(Comment $comment): void
    {
        $this->commentRepository->save($comment, flush: true);
    }

    /**
     * @param Comment $gallery
     *
     * @return void
     */
    public function delete(Comment $gallery): void
    {
        $this->commentRepository->delete($gallery, flush: true);
    }
}
