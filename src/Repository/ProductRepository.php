<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    const ITEMS_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findProducts($value = '1', $page = 1)
    {
        $firstResult = ($page - 1) * self::ITEMS_PER_PAGE;
        $qb = $this->createQueryBuilder('p');

        $qb->select("p.id,p.name,p.price, p.featured,p.currency,c.name category")
            ->andWhere('p.featured = :status')
            ->leftJoin('p.category', 'c')
            ->setParameter('status', $value)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();

        $criteria = Criteria::create()
            ->setFirstResult($firstResult)
            ->setMaxResults(self::ITEMS_PER_PAGE);
        $qb->addCriteria($criteria);

        //$doctrinePaginator = new DoctrinePaginator($qb);
        $paginator = new DoctrinePaginator($qb, $fetchJoinCollection = false);

        return $paginator;
    }

    public function findAllProducts($page = 1)
    {
        $firstResult = ($page - 1) * self::ITEMS_PER_PAGE;
        $qb = $this->createQueryBuilder('p');

        $qb->select("p.id,p.name,p.price, p.featured,p.currency,c.name category")
            ->leftJoin('p.category', 'c')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();

        $criteria = Criteria::create()
            ->setFirstResult($firstResult)
            ->setMaxResults(self::ITEMS_PER_PAGE);
        $qb->addCriteria($criteria);

        //$doctrinePaginator = new DoctrinePaginator($qb);
        $paginator = new DoctrinePaginator($qb, $fetchJoinCollection = false);

        return $paginator;
    }
}
