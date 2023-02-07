<?php

namespace App\Message;

use App\Entity\User;

class VerifyUserEmail
{
    public function __construct(private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}