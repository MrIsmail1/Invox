<?php

namespace App\Repository;

use App\Entity\CompanyDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompanyDetails>
 *
 * @method CompanyDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyDetails[]    findAll()
 * @method CompanyDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyDetails::class);
    }

//    /**
//     * @return CompanyDetails[] Returns an array of CompanyDetails objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CompanyDetails
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
