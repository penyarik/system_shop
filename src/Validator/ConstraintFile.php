<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ConstraintFile extends Constraint
{
    public string $message = 'You file extension "{{ extension }}" is not allowed';
    public string $messageToBig = 'Your file is too big';
    public int $maxSize;
    public array $extensions;

    #[HasNamedArguments]
    public function __construct(int $maxSize, array $extensions)
    {
        $this->maxSize = $maxSize;
        $this->extensions = $extensions;
        parent::__construct();
    }
}