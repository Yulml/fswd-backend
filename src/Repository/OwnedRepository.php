<?php

namespace App\Repository;

use App\Entity\Owned;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Owned>
 *
 * @method Owned|null find($id, $lockMode = null, $lockVersion = null)
 * @method Owned|null findOneBy(array $criteria, array $orderBy = null)
 * @method Owned[]    findAll()
 * @method Owned[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OwnedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Owned::class);
    }

    public function add(Owned $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Owned $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getQueryAll()
    {
        $qb = $this->createQueryBuilder('o');
        return $qb->getQuery();
    }

//    /**
//     * @return Owned[] Returns an array of Owned objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Owned
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
