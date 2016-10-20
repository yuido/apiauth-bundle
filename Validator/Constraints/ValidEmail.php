<?php

namespace Yuido\ApiAuthBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidEmail extends Constraint {

    public $message = "Este email no se encuentra registrado en el sistema";
   
    public function validatedBy() {
        return 'email_validator';
    }

}
