<?php

namespace App\Repository;

use App\Entity\Quotation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

public function countValidQuotations(): int
{
    $qb = $this->createQueryBuilder('q')
        ->select('COUNT(q.id)')
        ->where('q.isValid = :isValid')
        ->setParameter('isValid', true);

    return (int) $qb->getQuery()->getSingleScalarResult();
} 

}
