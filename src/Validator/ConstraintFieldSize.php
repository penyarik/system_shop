<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ConstraintFieldSize extends Constraint
{
    public string $message = 'Your description is too big"';
    public int $max;
    public int $min;

    #[HasNamedArguments]
    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
        parent::__construct();
    }
}