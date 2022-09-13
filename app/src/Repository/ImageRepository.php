<?php
/**
 * Image repository
 */

namespace App\Repository;

use App\Entity\Gallery;
use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ImageRepository
 *
 * @extends ServiceEntityRepository<Image>
 *
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    public const PAGINATOR_ITEMS_PER_PAGE = 5;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('image')
            ->orderBy('image.id', 'DESC');
    }

    /**
     * @param Gallery $gallery
     *
     * @return QueryBuilder
     */
    public function queryByGallery(Gallery $gallery): QueryBuilder
    {
        return $this->queryAll()
            ->select(
                'partial image.{id, title, description, path}',
            )
            ->where('image.gallery = :galleryId')
            ->setParameter('galleryId', $gallery->getId());
    }

    /**
     * @param Image $entity
     * @param bool  $flush
     *
     * @return void
     */
    public function save(Image $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @param Gallery $gallery
//     *
//     * @return QueryBuilder
//     */
//    public function queryByGallery(Gallery $gallery): QueryBuilder
//    {
//        return $this->createQueryBuilder('image')
//            ->select('image', 'gallery')
//            ->join('image.gallery', 'gallery')
//            ->where('gallery.id = :galleryId')
//            ->orderBy('image.id', 'DESC')
//            ->setParameter('galleryId', $gallery->getId());
//    }

    /**
     * @param Image $entity
     * @param bool  $flush
     *
     * @return void
     */
    public function delete(Image $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
