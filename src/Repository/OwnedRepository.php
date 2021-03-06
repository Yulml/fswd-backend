<?php

namespace App\Repository;

use App\Entity\Owned;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

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
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
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

    public function getAllOwnedPaginated(int $currentPage, int $registerPerPage): array
    {
        $query = $this->getQueryAll();
        $owneds = $this->paginator->paginate($query, $currentPage, $registerPerPage);
        $result = [];

        foreach ($owneds as $owned) {
            $result[] = $owned->toArray();
        }

        return $result;
    }

    public function sortByUser(): array {
        $queryResult = $this->createQueryBuilder('o')
        ->select('o, u')
        ->innerJoin('o.user','u')
        ->getQuery()
        ->getArrayResult();
    
        $userIds = [];
        foreach($queryResult as $owned) {
            $userIds[$owned['user']['id']] = $owned['user'];
        }

        return $userIds;
    }

    public function createOwned($user, $game): Owned
    {
        $owned = new Owned();
        $owned->setUser($user);
        $owned->setGame($game);

        $this->_em->persist($owned);
        $this->_em->flush();
        return $owned;
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
