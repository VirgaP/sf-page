<?php

namespace App\Repository;

use App\Entity\Heart;
use App\Entity\Animal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Heart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Heart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Heart[]    findAll()
 * @method Heart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeartRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Heart::class);
    }

    public function findAnimalIdWithHeart($animalId)
    {

        return $this->createQueryBuilder('h')
            ->leftJoin('h.user', 'user')
            ->addSelect('user')
            ->andWhere('c.animal = :id')
            ->setParameter('id', $animalId)
            ->getQuery()
            ->getResult()
            ;
    }

//
//
//DB::table('posts')
//->leftJoin('user_likes', function($join) {
//    $join->on('user_likes.post_id', '=', 'posts.id')
//        ->where('user_likes.user_id', '=', 1);
//})
//
//->select('posts.id as post_id', 'posts.title', 'user_likes as did_i_like')
//->groupBy('posts.id')
//->orderBy('posts.id');


    public function totalCount($heart)
    {
        $qb = $this->createQueryBuilder('h');
        $qb->where('h.animal = :id');
        $qb->andWhere('h.user = :user_id');
        $qb->setParameter('id', $heart);

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Heart[] Returns an array of Heart objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Heart
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
