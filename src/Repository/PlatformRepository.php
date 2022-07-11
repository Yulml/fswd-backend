<?php

namespace App\Repository;

use App\Entity\Platform;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Platform>
 *
 * @method Platform|null find($id, $lockMode = null, $lockVersion = null)
 * @method Platform|null findOneBy(array $criteria, array $orderBy = null)
 * @method Platform[]    findAll()
 * @method Platform[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlatformRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Platform::class);
    }

    public function add(Platform $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Platform $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getQueryAll()
    {
        $qb = $this->createQueryBuilder('p');
        return $qb->getQuery();
    }

    public function getAllPlatformsPaginated(int $currentPage, int $registerPerPage): array
    {
        $query = $this->getQueryAll();
        $platforms = $this->paginator->paginate($query, $currentPage, $registerPerPage);
        $result = [];

        foreach ($platforms as $platform) {
            $result[] = [
                'id' => $platform->getId(),
                'name' => $platform->getName(),
                'picture' => 'http://localhost:8080/uploads/platforms/' . $platform->getPicture(),
            ];
        }
        return $result;
    }
    
    public function getPlatformGames(Platform $platform)
    {
        $qb = $this->createQueryBuilder('p'); 
        // $games = $this->find($platform->getId());

        return [
            'id' => $platform->getId(),
            'name' => $platform->getName(),
            'picture' => 'http://localhost:8080/uploads/platforms/' . $platform->getPicture(),
            'games' => $this->createQueryBuilder('p')
            ->select('g.id, g.name, g.cover')
            ->innerJoin('p.games', 'g', Join::WITH, $qb->expr()->eq('p.id', $platform->getId())) //afinar query)
            ->getQuery()
            ->getArrayResult()
        ];
    }

//    /**
//     * @return Platform[] Returns an array of Platform objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Platform
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
