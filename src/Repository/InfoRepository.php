<?php

namespace App\Repository;

use App\Entity\Info;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Info>
 *
 * @method Info|null find($id, $lockMode = null, $lockVersion = null)
 * @method Info|null findOneBy(array $criteria, array $orderBy = null)
 * @method Info[]    findAll()
 * @method Info[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Info::class);
    }
// INSERT INTO et UPDATE
public function save(Info $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     // DELETE
     public function remove(Info $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Méthode pour afficher les infos récentes
/**
 * Récupère les infos récentes depuis la base de données.
 *
 * @param int $limit Le nombre maximal d'4'infos à récupérer
 * @return Info[] Retourne un tableau d'objets Info
 */
public function findRecentInfos($limit)
{
    return $this->createQueryBuilder('i') //  Création d'un QueryBuilder pour l'entité "Info"
        ->orderBy('i.createdAt', 'DESC') //  Trier les résultats par date de publication, du plus récent au plus ancien
        ->setMaxResults($limit) //  Limiter le nombre de résultats retournés à la valeur de $limit
        ->getQuery() // Génèrer la requête SQL à partir du QueryBuilder
        ->getResult(); //  Exécute la requête et renvoie les résultats sous forme de tableau d'objets "Info"
}
//    /**
//     * @return Info[] Returns an array of Info objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Info
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
