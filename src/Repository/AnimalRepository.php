<?php
/**
 * Created by PhpStorm.
 * User: virga
 * Date: 2018-05-18
 * Time: 14:26
 */

namespace App\Repository;

use App\Entity\Animal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Controller\AnimalController;

/**
 * @method Animal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Animal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Animal[]    findAll()
 * @method Animal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnimalRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Animal::class);
    }
//    public function findBy($animal)
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.isAvailable = :isAvailable')
//            ->setParameter('isAvailable', true)
//            ->orderBy('a.name', 'DESC')
//            ->getQuery()
//            ->execute();
//    }

    /**
     * @param int $page
     * @param int $perPage
     * @param $animal
     * @return Pagerfanta
     */
//    public function findAnimals($page = 1, $animal, $perPage = Animal::ANIMAL_PER_PAGE){
//        $paginator = new Pagerfanta(new DoctrineORMAdapter($this->findAll()));
//        $paginator->setMaxPerPage($perPage);
//        $paginator->setCurrentPage($page);
//
//        return $paginator;
//    }

//    /**
//     * @return Animal[] Returns an array of User objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
