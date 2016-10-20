<?php

namespace Yuido\ApiAuthBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use AppBundle\Entity\User;

class EmailValidator extends ConstraintValidator {
    
    private $em;
    private $userClass;
    
    public function __construct(\Doctrine\ORM\EntityManager $em, $userClass) {
        $this->em = $em;
        $this->userClass = $userClass;
    }

    public function validate($value, Constraint $constraint) {
        
        $user = $this->em->getRepository($this->userClass)->findOneBy([
            'email' => $value
        ]);
                
        
        if(!$user instanceof User){
            $this->context->buildViolation($constraint->message)
                    ->addViolation();
            return;
        }               
    }
}
