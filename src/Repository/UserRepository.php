<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, private UserPasswordHasherInterface $passwordHasher, private OwnedRepository $ownedRepository,private PaginatorInterface $paginator)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    public function getQueryAll()
    {
        $qb = $this->createQueryBuilder('u');
        return $qb->getQuery();
    }

    public function createUser(array $data): User
    {
        // add new user 
        $user = new User();
        $user->setEmail($data['username']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $data['password']
        );

        $user->setPassword($hashedPassword);
        $user->setRoles($data['roles']);
        $user->setNickname($data['nickname']);
        $user->setDob(new \DateTime($data['dateofbirth']), 'Y/m/d');
        $user->setAvatar($data['avatar']);

        $this->_em->persist($user);
        $this->_em->flush();
        return $user;
    }

    public function getUserGames(User $user)
    {
        // I want to show only the game name, game cover, genre name and platform name.

        $qb = $this->createQueryBuilder('u');
        
        return $this->createQueryBuilder('u')
        ->select('u, o, g, gn, pl')
        ->innerJoin('u.owneds', 'o', Join::WITH, $qb->expr()->eq('u.id', $user->getId())) //afinar query
        ->innerJoin('o.game', 'g')
        ->innerJoin('g.genre', 'gn')
        ->innerJoin('g.platform', 'pl')
        ->getQuery()
        ->getArrayResult();
    }

    public function getCollector(User $user)
    {
        $qb = $this->createQueryBuilder('u');
        
        return $this->createQueryBuilder('u')
            ->select('u.nickname, u.avatar')
            ->where($qb->expr()->eq('u.id', $user->getId()))
            ->getQuery()
            ->getArrayResult();
    }


    

    public function updateUser(User $user, array $data): ?User
    {
        if ($data['email'] !== '') {
            $user->setEmail($data['email']);
        }
        if ($data['roles'] !== '' || $data['roles'] !== 'ROLE_USER') {
            $user->setRoles($data['roles']);
        }
        if ($data['password'] !== '') {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
        }
        if ($data['nickname'] !== '') {
            $user->setNickname($data['nickname']);
        }
        if ($data['dateofbirth'] !== '') {
            $user->setDob(new \DateTime($data['dateofbirth']), 'Y/m/d');
        }
        if ($data['avatar'] !== '') {
            $user->setAvatar($data['avatar']);
        }

        $this->_em->flush();

        return $user;
    }

    public function getAllUsersPaginated(int $currentPage, int $registerPerPage): array
    {
        $query = $this->getQueryAll();
        $users = $this->paginator->paginate($query, $currentPage, $registerPerPage);
        $result = [];

        foreach ($users as $user) {
            $result[] = $user->toArray();
        }

        return $result;
    }
    
    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
