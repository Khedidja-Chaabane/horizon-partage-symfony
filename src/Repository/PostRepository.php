<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }
    // INSERT INTO et UPDATE
    public function add(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Nouvelle méthode pour trouver un post par son ID
    public function findPostById(int $id): ?Post
    {
        return $this->find($id);
    }

    // DELETE
    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Méthode pour trouver tous les posts par auteur
    public function findByUser($user)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.auteur = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

   // Méthode pour afficher les posts récents
/**
 * Récupère les posts récents depuis la base de données.
 *
 * @param int $limit Le nombre maximal de posts à récupérer
 * @return Produit[] Retourne un tableau d'objets Post
 */
public function findRecentPosts($limit)
{
    return $this->createQueryBuilder('p') //  Création d'un QueryBuilder pour l'entité "Post"
        ->orderBy('p.date_publication', 'DESC') //  Trier les résultats par date de publication, du plus récent au plus ancien
        ->setMaxResults($limit) //  Limiter le nombre de résultats retournés à la valeur de $limit
        ->getQuery() // Génèrer la requête SQL à partir du QueryBuilder
        ->getResult(); //  Exécute la requête et renvoie les résultats sous forme de tableau d'objets "Post"
}

    //    /**
    //     * @return Post[] Returns an array of Post objects
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

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
