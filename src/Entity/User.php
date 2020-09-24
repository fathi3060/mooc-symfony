<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
#use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Model\User as BaseUser;
// use Doctrine\ORM\Mapping\AttributeOverrides;
// use Doctrine\ORM\Mapping\AttributeOverride;


/**
 * @ORM\Table(name="oc_user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User extends BaseUser
{
    
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }

    /**
    * //@ORM\Column(name="username", type="string", length=255, unique=true)
    */
    protected $username;

    /**
    * //@ORM\Column(name="email", type="string", length=255, unique=true)
    */
    protected $email;

    /**
     * //@ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * //@ORM\Column(name="enabled", type="boolean", options={"default":true})
     */
    protected $enabled;

    /**
     * //@ORM\Column(name="lastLogin", type="datetime", options={"default":null})
     */
    protected $lastLogin;

    /**
     * @ORM\Column(name="locked", type="boolean", options={"default":false})
     */
    private $locked = false;

    /**
     * @ORM\Column(name="expired", type="datetime", nullable=true)
     */
    private $expired;

    /**
     * //@ORM\Column(name="salt", type="string", length=255)
     */
    protected $salt;

    /**
    * //@ORM\Column(name="roles", type="array")
    */
    protected $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function eraseCredentials()
    {
    }

    /**
     * Get the value of username
     */ 
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */ 
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of salt
     */ 
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set the value of salt
     *
     * @return  self
     */ 
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of enabled
     */ 
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set the value of enabled
     *
     * @return  self
     */ 
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }


    /**
     * Get the value of locked
     */ 
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set the value of locked
     *
     * @return  self
     */ 
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get the value of expired
     */ 
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Set the value of expired
     *
     * @return  self
     */ 
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }


    /**
     * Get the value of lastLogin
     */ 
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set the value of lastLogin
     *
     * @return  self
     */ 
    public function setLastLogin($lastLogin = null)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }
}
