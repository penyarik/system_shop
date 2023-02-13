<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

#[\Attribute]
class ConstraintFieldSizeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (strlen($value) > 999990) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}