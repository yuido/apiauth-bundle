<?php

namespace Yuido\ApiAuthBundle\Security;

use Doctrine\ORM\EntityManagerInterface;

class TokenManager {
    
    private $em;
    private $userClass;


    public function __construct(EntityManagerInterface $em, $userClass) {
        $this->em = $em;
        $this->userClass = $userClass;
    }
    
    public function createToken($user){
        
        $token = uniqid();
        
        $user->setToken($token);
        
        $this->em->persist($user);
        $this->em->flush();
        
        return $token;
    }
    
    public function removeToken($user){
        $user->setToken(null);
        $this->em->persist($user);
        $this->em->flush();
    }
    
    public function check($token){
        
        // TODO: comprobación de las reglas de autorización. Ahora mismo esto
        // afecta por igual a todas las rutas
        
        $user = $this->em->getRepository($this->userClass)->findOneBy(
                ['token' => $token]);
        
        if(!$user instanceof $this->userClass){
            return false;
        }
        
        return true;
    }
}
