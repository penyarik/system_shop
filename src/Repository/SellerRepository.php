<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Seller;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Seller>
 *
 * @method Seller|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seller|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seller[]    findAll()
 * @method Seller[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SellerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seller::class);
    }

    public function save(Seller $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Seller $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Seller[] Returns an array of Seller objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOneByField($value, string $field): ?Seller
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.' . $field . ' = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findFirst(): ?Seller
    {
        return $this->createQueryBuilder('s')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
