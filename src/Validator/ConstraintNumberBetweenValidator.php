<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

#[\Attribute]
class ConstraintNumberBetweenValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!is_int($value) || $value > $constraint->max || $value < $constraint->min) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ number }}', $value ?? 'empty')
                ->setParameter('{{ min }}', $constraint->min)
                ->setParameter('{{ max }}', $constraint->max)
                ->addViolation();
        }
    }
}