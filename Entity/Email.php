<?php

namespace Yuido\ApiAuthBundle\Entity;

use Yuido\ApiAuthBundle\Validator\Constraints\ValidEmail;
use Symfony\Component\Validator\Constraints\Email as AssertEmail;

class Email {
   
    /**
     *
     * @AssertEmail()
     * @ValidEmail()
     * 
     */
    private $email;
    
    function getEmail() {
        return $this->email;
    }

    function setEmail($email) {
        $this->email = $email;
    }


}
