<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annonce>
 *
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }
 // INSERT INTO et UPDATE
 public function add(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //DELETE
    public function remove(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //Filtre par catégorie
    /**
 * @param string $categorieName // Ce commentaire de ligne indique que la méthode attend une chaîne de caractères pour le nom de la catégorie.
 * @param bool $onlyActive       // Cette ligne indique que la méthode prend également un booléen pour déterminer si seules les annonces actives doivent être retournées.
 * @return Annonce[]             // Indique que cette méthode retournera un tableau d'objets de type `Annonce`.
 */
public function findByCategorieName(string $categorieName, bool $onlyActive = true): array
{
    // Créer un QueryBuilder pour construire une requête SQL dynamiquement
    $qb = $this->createQueryBuilder('a')
        // Faire une jointure entre l'entité 'Annonce' (alias 'a') et l'entité 'Categorie' (alias 'c')
        ->join('a.categorie', 'c')
        // Ajouter une condition pour que le nom de la catégorie corresponde au paramètre donné
        ->andWhere('c.nom = :categorieName')
        // Définir la valeur du paramètre :categorieName avec la variable $categorieName
        ->setParameter('categorieName', $categorieName);

    // Vérifier si seules les annonces actives doivent être retournées
    if ($onlyActive) {
        // Ajouter une condition pour que le statut de l'annonce soit vrai (actif)
        $qb->andWhere('a.status = :status')
           // Définir la valeur du paramètre :status à vrai (true)
           ->setParameter('status', true);
    }

    // Exécuter la requête construite et obtenir les résultats sous forme de tableau d'objets Annonce
    return $qb->getQuery()->getResult();
}

//    /**
//     * @return Annonce[] Returns an array of Annonce objects
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

//    public function findOneBySomeField($value): ?Annonce
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
