<?php

namespace App\Repository;

use App\Entity\Action;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Action>
 *
 * @method Action|null find($id, $lockMode = null, $lockVersion = null)
 * @method Action|null findOneBy(array $criteria, array $orderBy = null)
 * @method Action[]    findAll()
 * @method Action[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Action::class);
    }
 // INSERT INTO et UPDATE
 public function add(Action $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     // DELETE
     public function remove(Action $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    // Méthode pour trouver toutes les actions par catégorie
    //public function findByCategory($category)
   // {
       // return $this->createQueryBuilder('a')
          //  ->andWhere('a.categorie = :category')
          //  ->setParameter('category', $category)
           // ->orderBy('a.id', 'ASC')
           // ->getQuery()
          //  ->getResult()
     //   ;
   // }
    /**
     * @param string $categorieName
     * @return Action[]
     */
    public function findByCategorieName(string $categorieName): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.categorie', 'c')
            ->andWhere('c.nom = :categorieName')
            ->setParameter('categorieName', $categorieName)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Action[] Returns an array of Action objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Action
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
