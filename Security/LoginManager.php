<?php

namespace Yuido\ApiAuthBundle\Security;

use Symfony\Component\HttpFoundation\JsonResponse;

class LoginManager {

    private $userManager;
    private $encoderFactory;
    private $tokenManager;

    public function __construct($userManager, $encoderFactory, $tokenManager) {
        $this->userManager = $userManager;
        $this->encoderFactory = $encoderFactory;
        $this->tokenManager = $tokenManager;
    }

    public function login($username, $password) {

        $user = $this->userManager->findUserByUsernameOrEmail($username);
               
        $response = new JsonResponse([]);
        $response->setStatusCode(401);

        if (!$user) {
            return $response;
        }

        $userArr = [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'secondname' => $user->getSecondname(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'role' => 'admin',
            'changeEmail' => $user->getChangeEmail()
        ];

        if ($user->getToken()) {
            
            $response = new JsonResponse([
                    'token' => $user->getToken(),
                    'user' => $userArr
                ]);
            
        } else {
            $encoder = $this->encoderFactory->getEncoder($user);
            if ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {

                $token = $this->tokenManager->createToken($user);

                $response = new JsonResponse([
                    'token' => $token,
                    'user' => $userArr
                ]);
            }
        }


        return $response;
    }

    public function logout($token) {

        $user = $this->userManager->findUserBy([
            'token' => $token
        ]);
        
        $response = new JsonResponse();
        if(!$user instanceof \Yuido\ApiAuthBundle\Entity\User){
            $response->setStatusCode(404);
        }else{     
            $this->tokenManager->removeToken($user);                        
        }

        return $response;
    }

}
