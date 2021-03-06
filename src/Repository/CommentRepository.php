<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }


    public function findPaginated(int $limit, int $first, Trick $trick)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.trick = :trick')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($first)
            ->setParameter('trick', $trick)
            ->getQuery()
            ->getResult()
        ;
    }
}
