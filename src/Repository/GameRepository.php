<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\AST\Join;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Game::class);
    }

    public function add(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getQueryAll()
    {
        $qb = $this->createQueryBuilder('g');
        return $qb->getQuery();
    }

    public function getAllGamesPaginated(int $currentPage, int $registerPerPage): array
    {
        $query = $this->getQueryAll();
        $games = $this->paginator->paginate($query, $currentPage, $registerPerPage);
        $result = [];

        foreach ($games as $game) {
            $result[] = [
                'name' => $game->getName(),
                'platform' => $game->getPlatform()->getName(),
                'genre' => $game->getGenre()->getName(),
                'cover' => 'http://localhost:8080/uploads/games/' . $game->getCover(),
            ];
        }
        return $result;
    }

    //SQL para obtener los juegos por plataforma
    //SELECT game.name, game.cover, genre.name genre FROM game INNER JOIN genre ON game.genre_id = genre.id WHERE platform_id LIKE 3; 

    public function GetAllPerPlatform()
    {
        $qb = $this->createQueryBuilder('g');
        return $this->createQueryBuilder('g')
            ->select('g, gn, pl')
            ->innerJoin('g.genre', 'gn')
            ->innerJoin('g.platform', 'pl')
            ->getQuery()
            ->getArrayResult();
    }

//    /**
//     * @return Game[] Returns an array of Game objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Game
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
