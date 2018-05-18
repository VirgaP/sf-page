<?php
/**
 * Created by PhpStorm.
 * User: tadas
 * Date: 2018-05-17
 * Time: 10:03
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="message")
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Vardo laukelis negali būti tuščias")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="El.pašto laukelis negali būti tuščias")
     * @Assert\Email(message="Neteisingas el.pašto adresas")
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Telefono numerio laukelis negali būti tuščias")
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Žinutės laukelis negali būti tuščias")
     */
    private $message;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSeen = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param mixed $phoneNumber
     */
    public function setPhoneNumber($phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getisSeen()
    {
        return $this->isSeen;
    }

    /**
     * @param mixed $isSeen
     */
    public function setIsSeen($isSeen): void
    {
        $this->isSeen = $isSeen;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTime('now');
    }


}