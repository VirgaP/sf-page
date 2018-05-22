<?php
/**
 * Created by PhpStorm.
 * User: tadas
 * Date: 2018-05-20
 * Time: 12:02
 */

namespace App\Repository;


use App\Entity\Animal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Animal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Animal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Animal[]    findAll()
 * @method Animal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnimalTestRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Animal::class);
    }

    public function filterBoth($animal, $available)
    {
        return $this->createQueryBuilder('animal')
            ->andWhere('animal.species = :type')
            ->andWhere('animal.isAvailable = :available')
            ->setParameters(['type' => $animal, 'available' => $available])
            ->getQuery()
            ->getResult()
            ;
    }

    public function filterAnimal($animal)
    {
        return $this->createQueryBuilder('animal')
            ->andWhere('animal.species = :type')
            ->setParameter('type', $animal)
            ->getQuery()
            ->getResult()
            ;
    }

    public function filterAvailable($available)
    {
        return $this->createQueryBuilder('animal')
            ->andWhere('animal.isAvailable = :available')
            ->setParameter('available', $available)
            ->getQuery()
            ->getResult()
            ;
    }

}