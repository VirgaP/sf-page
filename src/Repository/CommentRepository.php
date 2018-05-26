<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findAllApprovedByAnimal($animalId)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.user', 'user')
            ->addSelect('user')
            ->andWhere('c.animal = :id')
            ->andWhere('c.isApproved = 1')
            ->setParameter('id', $animalId)
            ->orderBy('c.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllWhereNotApproved()
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.animal', 'animal')
            ->addSelect('animal')
            ->andWhere('c.isApproved = 0')
            ->orderBy('c.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllWhereApproved()
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.animal', 'animal')
            ->addSelect('animal')
            ->andWhere('c.isApproved = 1')
            ->orderBy('c.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function setAsApproved($commentId)
    {
        return $this->createQueryBuilder('comment')
            ->update('App:Comment', 'c')
            ->set('c.isApproved', 1)
            ->andWhere('c.id = :id')
            ->setParameter('id', $commentId)
            ->getQuery()
            ->execute();
    }

    public function countAllUnapprovedComments()
    {
        return $this->createQueryBuilder('comment')
            ->andWhere('comment.isApproved = 0')
            ->select('COUNT(comment) AS commentCount')
            ->getQuery()
            ->getSingleScalarResult();
    }



}
