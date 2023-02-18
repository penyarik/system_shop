<?php

namespace App\Repository;

use App\CustomEntity\TranslationType;
use App\Entity\Category;
use App\Entity\Seller;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //add logic to check if no products related to this category
    public function findPossibleParents(int $categoryId): array
    {
        return $this->createQueryBuilder('c')
            ->where(
                $this->getEntityManager()->getExpressionBuilder()->eq(
                    '(SELECT COUNT(prod.name) from App\Entity\Product prod WHERE prod.category_id = c.id)',
                    0
                )
            )
            ->andWhere('c.id != :val')
            ->setParameter('val', $categoryId)
            ->orderBy('c.updated_date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //add check if has products
    public function isDeletable(int $categoryId): bool
    {
        $data = $this->createQueryBuilder('c')
            ->andWhere('c.parent_id = :parent')
            ->setParameter('parent', $categoryId)
            ->getQuery()
            ->getResult();

        return count($data) === 0;
    }

//    /**
//     * @return Category[] Returns an array of Category objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOneByField($value, string $field): ?array
    {

        return $this->createQueryBuilder('c')
            ->andWhere('c.' . $field . ' = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();
    }

    public function findByIdAndSeller(int $sellerId, int $id): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.seller_id = :seller')
            ->andWhere('c.id  = :id')
            ->setParameter('seller', $sellerId)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getSellerMainCategories(int $sellerId, int $localeId): ?array
    {
        return $this->createQueryBuilder('c')
            ->select('t')
            ->andWhere('c.seller_id = :seller')
            ->andWhere('c.parent_id IS NULL')
            ->leftJoin(
                'App\Entity\Translation',
                't',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'c.id = t.entity_id and t.entity_type = '.TranslationType::CATEGORY->value.' and t.locale = '.$localeId.''
            )
            ->setParameter('seller', $sellerId)
            ->getQuery()
            ->setMaxResults(10)
            ->getResult();
    }
}
