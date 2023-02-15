<?php

namespace App\Validator;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

#[\Attribute]
class ConstraintFileValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        /**
         * @var $val UploadedFile
         */
        $size = 0;
        foreach ($value as $val) {
            $size += $val->getSize();
            $extension = explode('.', $val->getClientOriginalName())[1];
            if (!in_array($extension, $constraint->extensions)) {
                $this->context->buildViolation($constraint->message . ' List of allowed extensions: ' . implode(',', $constraint->extensions))
                    ->setParameter('extension', $extension)
                    ->addViolation();
                break;
            }
        }

        if ($size > $constraint->maxSize) {
            $this->context->buildViolation($constraint->messageToBig)
                ->addViolation();
        }
    }
}