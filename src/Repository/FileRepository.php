<?php

namespace App\Repository;

use App\Entity\File;
use App\Entity\Translation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<File>
 *
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function save(File $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(File $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByNames(array $names): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.name IN (:name)')
            ->setParameter('name', implode(',', $names))
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return File[] Returns an array of File objects
//     */
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByEntityIdAndEntityName(int $entityId, string $entityName): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.entity_id = :id')
            ->andWhere('t.entity_name = :name')
            ->setParameter('id', $entityId)
            ->setParameter('name', $entityName)
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?File
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
