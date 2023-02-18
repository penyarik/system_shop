<?php

namespace App\Repository;

use App\Entity\Translation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Translation>
 *
 * @method Translation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Translation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Translation[]    findAll()
 * @method Translation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Translation::class);
    }

    public function save(Translation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Translation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return EnTranslation[] Returns an array of EnTranslation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    /**
     * @return Translation[]
     */
    public function findByField($value, string $field, int $limitation = 10): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.' . $field . ' = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults($limitation)
            ->getQuery()
            ->getResult();
    }

    public function findByEntityIdAndEntityType(int $entityId, int $entityType): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.entity_id = :id')
            ->andWhere('t.entity_type = :type')
            ->setParameter('id', $entityId)
            ->setParameter('type', $entityType)
            ->getQuery()
            ->getResult();
    }

    public function findByLocaleAndEntityIdtAndEntityType(int $entityId, int $entityType, int $locale): ?Translation
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.entity_id = :id')
            ->andWhere('t.entity_type = :type')
            ->andWhere('t.locale = :locale')
            ->setParameter('id', $entityId)
            ->setParameter('locale', $locale)
            ->setParameter('type', $entityType)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
