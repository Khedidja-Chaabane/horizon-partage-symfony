<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    
 //new user et UPDATE PROFILE
 
 public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //Delete
    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
 * Used to upgrade (rehash) the user's password automatically over time.
 * (Utilisé pour mettre à niveau (rehacher) le mot de passe de l'utilisateur automatiquement au fil du temps.)
 *Cela peut être nécessaire lorsque l'algorithme de hachage change ou doit être renforcé.
 */
public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
{
    // Vérifier si l'objet $user est bien une instance de la classe User.
    // Si ce n'est pas le cas, une exception est lancée.
    // Cela assure que cette méthode ne travaille qu'avec des objets de type User.
    if (!$user instanceof User) {
        // Lancer une exception si $user n'est pas de type User.
        // UnsupportedUserException indique que le type d'utilisateur fourni n'est pas supporté.
        throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
    }

    // Mettre à jour le mot de passe de l'utilisateur avec le nouveau mot de passe haché.
    // Cela remplace l'ancien mot de passe par le nouveau.
    $user->setPassword($newHashedPassword);

    // Sauvegarder l'objet utilisateur avec le nouveau mot de passe dans la base de données.
    // Le deuxième argument 'true' peut indiquer que la sauvegarde doit être forcée immédiatement.
    $this->save($user, true);
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
