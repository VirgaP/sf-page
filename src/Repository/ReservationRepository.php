<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findAllByAnimal($animalId, $date)
    {
        return $this->createQueryBuilder('r')
            ->select('r.hour')
            ->andWhere('r.animal = :id')
            ->andWhere('r.date = :date')
            ->setParameters(['id' => $animalId, 'date' => $date])
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function findAllReservationsWhereNotApproved()
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.user', 'user')
            ->leftJoin('r.animal', 'animal')
            ->addSelect('user')
            ->addSelect('animal')
            ->andWhere('r.isApproved = 0')
            ->orderBy('r.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllReservationsWhereApproved()
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.user', 'user')
            ->leftJoin('r.animal', 'animal')
            ->addSelect('user')
            ->addSelect('animal')
            ->andWhere('r.isApproved = 1')
            ->orderBy('r.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function setReservationAsApproved($reservationId)
    {
        return $this->createQueryBuilder('reservation')
            ->update('App:Reservation', 'r')
            ->set('r.isApproved', 1)
            ->andWhere('r.id = :id')
            ->setParameter('id', $reservationId)
            ->getQuery()
            ->execute();
    }

    public function countAllUnapprovedReservations()
    {
        return $this->createQueryBuilder('reservation')
            ->andWhere('reservation.isApproved = 0')
            ->select('COUNT(reservation) AS reservationCount')
            ->getQuery()
            ->getSingleScalarResult();
    }


}
