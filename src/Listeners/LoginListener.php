<?php
/**
 * Created by PhpStorm.
 * User: virga
 * Date: 2018-05-24
 * Time: 14:30
 */

namespace App\Listeners;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        // Get the User entity
        $user = $event->getAuthenticationToken()->getUser();

        // Update the entity field here
        if ($user == 'ROLE_ADMIN' || $user == 'ROLE_USER') {
            return $user->setLastLogin(new \DateTime('now'));
        } else {
            return;
        }

        // Persist the data to database
        $this->em->persist($user);
        $this->em->flush();
    }
}