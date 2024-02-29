<?php

namespace App\Repository;

use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Invoice>
 *
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

//    /**
//     * @return Invoice[] Returns an array of Invoice objects
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

//    public function findOneBySomeField($value): ?Invoice
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



public function countInvoicesByStatusAndUser(string $status, User $user): int
{
    $qb = $this->createQueryBuilder('i')
        ->select('COUNT(i.id)')
        ->innerJoin('i.users', 'u')
        ->where('i.status = :status')
        ->andWhere('u.id = :userId')
        ->setParameter('status', $status)
        ->setParameter('userId', $user->getId());

    return (int) $qb->getQuery()->getSingleScalarResult();
}


public function getTotalByMonthForLastYearForUser(User $user): array
{
    $conn = $this->getEntityManager()->getConnection();
    $sql = '
        SELECT SUM(i.total) as total, EXTRACT(MONTH FROM i.created_at) as month, EXTRACT(YEAR FROM i.created_at) as year
        FROM invoice i
        JOIN user_invoice ui ON i.id = ui.invoice_id
        WHERE i.created_at >= :date AND ui.user_id = :userId
        GROUP BY EXTRACT(YEAR FROM i.created_at), EXTRACT(MONTH FROM i.created_at)
        ORDER BY year ASC, month ASC
    ';
    $stmt = $conn->executeQuery($sql, [
        'date' => (new \DateTime())->modify('-12 months')->format('Y-m-d'),
        'userId' => $user->getId(),
    ]);

    return $stmt->fetchAllAssociative();
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
