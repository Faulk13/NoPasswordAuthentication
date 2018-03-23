<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="administrator")
 *
 * @UniqueEntity({"email"})
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     * @Assert\Email()
     */
    private $email;

    /**
     * @var array
     *
     * @ORM\Column(type="simple_array", nullable=false)
     *
     * @Assert\Count(min="1")
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $connectionToken;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $connectionAt;


    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->email;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return string
     */
    public function getUsername()
    {
       return $this->email;
    }

    /**
     * @return string
     */
    public function getConnectionToken()
    {
        return $this->connectionToken;
    }

    /**
     * @param string $connectionToken
     */
    public function setConnectionToken($connectionToken)
    {
        $this->connectionToken = $connectionToken;
    }

    /**
     * @return DateTime|null
     */
    public function getConnectionAt(): ?DateTime
    {
        return $this->connectionAt;
    }

    /**
     * @param DateTime|null $connectionAt
     */
    public function setConnectionAt($connectionAt)
    {
        $this->connectionAt = $connectionAt;
    }

    /**
     * @param $lifetime
     *
     * @return bool
     */
    public function isTokenExpired($lifetime)
    {
        if ($this->connectionAt instanceof DateTime && $this->connectionAt->getTimestamp() + $lifetime > time()) {
            return false;
        }

        return true;
    }
}
