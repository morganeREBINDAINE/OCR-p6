<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

     /**
      * @return Trick[] Returns an array of Trick objects
      */
    public function findPaginated(int $limit, int $first)
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.id', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($first)
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(Trick $trick)
    {
        $this->getEntityManager()->persist($trick);
        return $this->getEntityManager()->flush();
    }
}
