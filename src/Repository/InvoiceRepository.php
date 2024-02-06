<?php

namespace App\Repository;

use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

public function findByCreatedAtRange($startDate, $endDate): array
{
    $qb = $this->createQueryBuilder('i')
        ->where('i.createdAt >= :startDate')
        ->andWhere('i.createdAt <= :endDate')
        ->setParameter('startDate', $startDate ? $startDate->format('Y-m-d 00:00:00') : null)
        ->setParameter('endDate', $endDate ? $endDate->format('Y-m-d 23:59:59') : null)
        ->getQuery();

    return $qb->getResult();
}

public function countInvoicesByStatus(string $status): int
{
    $qb = $this->createQueryBuilder('i')
        ->select('COUNT(i.id)')
        ->where('i.status = :status')
        ->setParameter('status', $status);

    return (int) $qb->getQuery()->getSingleScalarResult();
}

public function getTotalByMonthForLastYear(): array
{
    $conn = $this->getEntityManager()->getConnection();
    $sql = '
        SELECT SUM(total) as total, EXTRACT(MONTH FROM created_at) as month, EXTRACT(YEAR FROM created_at) as year
        FROM invoice
        WHERE created_at >= :date
        GROUP BY EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)
        ORDER BY year ASC, month ASC
    ';
    $stmt = $conn->executeQuery($sql, ['date' => (new \DateTime())->modify('-12 months')->format('Y-m-d')]);

    return $stmt->fetchAllAssociative();
}




public function findBySearchQuery(string $query): array
{
    return $this->createQueryBuilder('i')
        ->leftJoin('i.customer', 'c')
        ->where('c.firstName LIKE :searchTerm')
        ->orWhere('c.lastName LIKE :searchTerm')
        ->setParameter('searchTerm', '%'.$query.'%')
        ->getQuery()
        ->getResult();
}

}
