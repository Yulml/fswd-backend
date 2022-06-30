<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
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
    public function __construct(ManagerRegistry $registry, private UserPasswordHasherInterface $passwordHasher)
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
        $qb = $this->createQueryBuilder('userqb');
        return $qb->getQuery();
    }

    public function createUser(array $data): User
    {
        // add new user 
        // we control the avatar because we should always have default_avatar.jpg as a fallback
        $user = new User();
        $user->setEmail($data['email']);
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

    public function editUser(User $user)
    {

        return $user;
    }


    //SQL para obtener los juegos por plataforma
    //SELECT game.name, game.cover, genre.name genre FROM game INNER JOIN genre ON game.genre_id = genre.id WHERE platform_id LIKE 3; 

    // SQL para obtener los juegos por usuario
    // SELECT game.name, game.cover, genre.name genre, platform.name platform FROM GAME 
    // INNER JOIN genre ON game.genre_id = genre.id 
    // INNER JOIN platform ON game.platform_id = platform.id
    // WHERE game.id IN (SELECT game_id FROM owned WHERE user_id LIKE 1)  <--- $user->getId()

    public function getUserGames(User $user)
    {
        $qb = $this->createQueryBuilder('u');
        return $this->createQueryBuilder('u')
            ->select('u, o, g')
            ->leftJoin('u.owneds', 'o', Join::WITH, $qb->expr()->eq('u.id', $user->getId())) //afinar query
            ->leftJoin('o.game', 'g')
            ->getQuery()
            ->getArrayResult();
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
