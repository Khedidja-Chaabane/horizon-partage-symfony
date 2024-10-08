<?php

namespace App\Repository;

use App\Entity\Don;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Don>
 *
 * @method Don|null find($id, $lockMode = null, $lockVersion = null)
 * @method Don|null findOneBy(array $criteria, array $orderBy = null)
 * @method Don[]    findAll()
 * @method Don[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonRepository extends ServiceEntityRepository
{
    // Le constructeur permet d'initialiser le repository pour la gestion de l'entité "Don"
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Don::class);
    }

    //save don dans la base de données
    public function save(int $total, $user): void
    {
        // Récupération du gestionnaire d'entité pour interagir avec la base de données
        $entityManager = $this->getEntityManager();
        // Création d'un nouvel objet Don
        $don = new Don();
        // On définit le montant total à enregistrer
        $don->setMontant($total);
        // On assigne le donateur qui est l'utilisateur connecté
        $don->setDonateur($user);
        // On définit la date actuelle
        $don->setDateDon(new \DateTimeImmutable());
        // On prépare l'enregistrement en demandant à Doctrine de suivre cet objet pour un futur enregistrement
        // On persiste le don total
        $entityManager->persist($don);

        // On enregistre dans la base de données
        $entityManager->flush();
    }


    /**
     * Cette méthode récupère tous les dons effectués par un utilisateur spécifique.
     * 
     * @param User $user L'utilisateur dont on veut récupérer les dons.
     * @return Don[] Une liste d'objets Don associés à cet utilisateur.
     */
    public function findDonsByUser($user)
    {
        return $this->createQueryBuilder('d')
            ->where('d.donateur = :user') // On sélectionne les dons où le donateur est l'utilisateur passé en paramètre
            ->setParameter('user', $user) // On lie l'utilisateur à la requête
            ->orderBy('d.dateDon', 'DESC') // On peut trier les dons par date (du plus récent au plus ancien)
            ->getQuery()
            ->getResult(); // On récupère les résultats sous forme d'un tableau d'objets Don
    }
    
    //    /**
    //     * @return Don[] Returns an array of Don objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Don
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
