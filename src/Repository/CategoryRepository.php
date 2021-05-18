<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Common\Collections\Criteria;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    CONST ITEMS_PER_PAGE=4;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findAllCategory($page=1)
    {
        $firstResult = ($page -1) * self::ITEMS_PER_PAGE;
        $qb = $this->createQueryBuilder('p');

        $qb ->select('p.id,p.name,p.description,p.active')
        ->andWhere('p.active = :status')
        ->setParameter('status', true)
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
