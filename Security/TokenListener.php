<?php

namespace Yuido\ApiAuthBundle\Security;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TokenListener {

    private $em;
    private $tokenManager;

    public function __construct(EntityManagerInterface $em, $tokenManager) {
        $this->em = $em;
        $this->tokenManager = $tokenManager;
    }

    public function onKernelController(FilterControllerEvent $event) {
//        $controller = $event->getController();      
//       
//        
//        if(!$controller[0] instanceof Controller) return;
//        
//        $request = $controller[0]->getRequest();
//        
//        if($request->get('_route') === 'login' 
//                || $request->get('_route') === 'get_forgot_password_token'
//                || $request->get('_route') === 'set_password'
//                ) return;
//                
//        if (!$request->get('token')) {
//            throw new AccessDeniedHttpException('This action needs a token!');
//        }
//
//        if (!$this->tokenManager->check($request->get('token'))) {
//            throw new AccessDeniedHttpException('This token is not valid!');
//        }
    }

}
