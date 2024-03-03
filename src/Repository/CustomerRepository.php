<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 *
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

//    /**
//     * @return Customer[] Returns an array of Customer objects
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

//    public function findOneBySomeField($value): ?Customer
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
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

}
