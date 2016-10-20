<?php

namespace Yuido\ApiAuthBundle;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserManager {

    protected $encoderFactory;
    protected $em;
    protected $userClass;

    public function __construct(EncoderFactoryInterface $encoderFactory, $em,  $userClass) {
        $this->encoderFactory = $encoderFactory;
        $this->em = $em;
        $this->userClass = $userClass;
                
        $this->repository = $this->em->getRepository($userClass);
    }

    /**
     * Returns an empty user instance
     *
     * @return UserInterface
     */
    public function createUser() {
        $class = $this->userClass;
        $user = new $class;               

        return $user;
    }
    
    public function updateUser($user, $andFlush = true){                             
        
        $this->updatePassword($user);
                

        $this->em->persist($user);        
        if ($andFlush) {
            $this->em->flush();
        }
    }

    public function deleteUser(UserInterface $user) {
        $this->em->remove($user);
        $this->em->flush();
    }

    public function findUserBy(array $criteria) {      
        
        $user = $this->repository->findOneBy($criteria);
        
        if(!$user instanceof $this->userClass) return false;
        
        return $user;
    }
    
    public function findUsers()
    {
        return $this->repository->findAll();
    }
    

    /**
     * Finds a user by email
     *
     * @param string $email
     *
     * @return UserInterface
     */
    public function findUserByEmail($email) {        
        return $this->findUserBy(array('email' => $email));
    }

    /**
     * Finds a user by username
     *
     * @param string $username
     *
     * @return UserInterface
     */
    public function findUserByUsername($username) {
        return $this->findUserBy(array('username' => $username));
    }

    /**
     * Finds a user either by email, or username
     *
     * @param string $usernameOrEmail
     *
     * @return UserInterface
     */
    public function findUserByUsernameOrEmail($usernameOrEmail) {
        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    /**
     * Finds a user either by confirmation token
     *
     * @param string $token
     *
     * @return UserInterface
     */
    public function findUserByForgotpasstoken($token) {
        return $this->findUserBy(array('forgotpasstoken' => $token));
    }

    /**
     * {@inheritDoc}
     */
    public function updatePassword($user) {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $encoder = $this->getEncoder($user);
            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
        }
        
    }

    protected function getEncoder($user) {
        return $this->encoderFactory->getEncoder($user);
    }

}
