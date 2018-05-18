<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @return Message[] Returns an array of User objects
     */
    public function findAllWhereIsNotSeen()
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.isSeen = 0')
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllWhereIsSeen()
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.isSeen = 1')
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function setAsSeen($messageId)
    {
        return $this->createQueryBuilder('msg')
            ->update('App:Message', 'm')
            ->set('m.isSeen', 1)
            ->andWhere('m.id = :id')
            ->setParameter('id', $messageId)
            ->getQuery()
            ->execute();
    }


}
