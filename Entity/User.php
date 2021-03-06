<?php

namespace Yuido\ApiAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 * @ORM\MappedSuperclass()
 */
abstract class User implements UserInterface
{
    
    private $oldPassword;

    public function getOldPassword(){
        return $this->oldPassword;
    }
    public function setOldPassword($p){
        $this->oldPassword = $p;
    }
    
    private $plainPassword;
    
    function getPlainPassword() {
        return $this->plainPassword;
    }

    function setPlainPassword($plainPassword) {
        $this->plainPassword = $plainPassword;
    }

   
        
    protected $id;
    
    /**
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;
    
    /**     
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;
    
    /**     
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = true;
    
    /**     
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     */
    private $salt;
    
    /**     
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password;
    
    /**     
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;
    
    /**     
     * @ORM\Column(name="locked", type="boolean")
     */
    private $locked;
    
    /**     
     * @ORM\Column(name="expired", type="boolean")
     */
    private $expired;
    
    /**     
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    private $expiresAt;
    
    /**
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;
    
    /**
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     *
     * @ORM\Column(name="secondname", type="string", length=255, nullable=true)
     */
    private $secondname;
    
    /**
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;
    
    /**
     *
     * @ORM\Column(name="forgotpasstoken", type="string", length=255, nullable=true)
     */
    private $forgotpasstoken;
    
    /**
     * @ORM\Column(name="forgotpass_token_validity", type="datetime", nullable=true)
     */
    private $forgotpassTokenValidity;
        
    /**
     *
     * @ORM\Column(name="roles", type="text", nullable=true)
     */
    private $roles;


    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->enabled = true;
        $this->locked = false;
        $this->expired = false;        
    }
    
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     *
     * @return User
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     *
     * @return User
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     *
     * @return User
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean
     */
    public function getExpired()
    {
        return $this->expired;
    }
   
    /**
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     *
     * @return User
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set secondname
     *
     * @param string $secondname
     *
     * @return User
     */
    public function setSecondname($secondname)
    {
        $this->secondname = $secondname;

        return $this;
    }

    /**
     * Get secondname
     *
     * @return string
     */
    public function getSecondname()
    {
        return $this->secondname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set forgotpasstoken
     *
     * @param string $forgotpasstoken
     *
     * @return User
     */
    public function setForgotpasstoken($forgotpasstoken)
    {
        $this->forgotpasstoken = $forgotpasstoken;

        return $this;
    }

    /**
     * Get forgotpasstoken
     *
     * @return string
     */
    public function getForgotpasstoken()
    {
        return $this->forgotpasstoken;
    }

    /**
     * Set forgotpassTokenValidity
     *
     * @param \DateTime $forgotpassTokenValidity
     *
     * @return User
     */
    public function setForgotpassTokenValidity($forgotpassTokenValidity)
    {
        $this->forgotpassTokenValidity = $forgotpassTokenValidity;

        return $this;
    }

    /**
     * Get forgotpassTokenValidity
     *
     * @return \DateTime
     */
    public function getForgotpassTokenValidity()
    {
        return $this->forgotpassTokenValidity;
    }
    
    public function getRoles() {
        return $this->roles;
    }

    public function setRoles($roles) {
        $this->roles = $roles;
    }

    
    public function eraseCredentials(){}

}
