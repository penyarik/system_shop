<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ConstraintNumberBetween extends Constraint
{
    public string $message = 'The number "{{ number }}" should be between "{{ min }} and "{{ max }}"';
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