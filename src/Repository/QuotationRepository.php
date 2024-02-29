<?php

namespace App\Repository;

use App\Entity\Quotation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Quotation>
 *
 * @method Quotation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quotation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quotation[]    findAll()
 * @method Quotation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quotation::class);
    }

//    /**
//     * @return Quotation[] Returns an array of Quotation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Quotation
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function findByUserAndCreatedAtRange($user, $startDate, $endDate): array
{
    $qb = $this->createQueryBuilder('i')
        ->innerJoin('i.users', 'u') // Joindre la table des utilisateurs
        ->where('u.id = :userId') // Filtrer par l'ID de l'utilisateur
        ->andWhere('i.createdAt >= :startDate')
        ->andWhere('i.createdAt <= :endDate')
        ->setParameter('userId', $user->getId()) // Assurez-vous de passer l'objet User entier à la méthode
        ->setParameter('startDate', $startDate ? $startDate->format('Y-m-d 00:00:00') : null)
        ->setParameter('endDate', $endDate ? $endDate->format('Y-m-d 23:59:59') : null)
        ->getQuery();

    return $qb->getResult();
}

public function countValidQuotationsByUser(User $user): int
{
    $qb = $this->createQueryBuilder('q')
        ->select('COUNT(q.id)')
        ->innerJoin('q.users', 'u')
        ->where('q.isValid = :isValid')
        ->andWhere('u.id = :userId')
        ->setParameter('isValid', true)
        ->setParameter('userId', $user->getId());

    return (int) $qb->getQuery()->getSingleScalarResult();
}


 public function createQueryBuilderForUser(User $user, string $sort = 'a.id', string $direction = 'asc'): QueryBuilder
    {
        // Initialise le QueryBuilder avec un filtre sur l'utilisateur
        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.users', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId());

        // Ajoute un tri basé sur les paramètres fournis
        if (!empty($sort) && !empty($direction)) {
            $qb->orderBy($sort, $direction);
        }

        return $qb;
    }

     public function findByUser(User $user)
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.users', 'u') // Assurez-vous que 'users' est le nom correct de la propriété dans Invoice qui référence User
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
    }

}
