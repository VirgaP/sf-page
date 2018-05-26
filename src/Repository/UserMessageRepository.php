<?php

namespace App\Repository;

use App\Entity\UserMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMessage[]    findAll()
 * @method UserMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserMessage::class);
    }

    public function findAllByUser($userId)
    {
        return $this->createQueryBuilder('msg')
            ->andWhere('msg.user = :id')
            ->setParameter('id', $userId)
            ->orderBy('msg.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countAllUnseenMessages()
    {
        return $this->createQueryBuilder('message')
            ->andWhere('message.isSeen = 0')
            ->select('COUNT(message) AS messageCount')
            ->getQuery()
            ->getSingleScalarResult();
    }


}
