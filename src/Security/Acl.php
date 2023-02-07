<?php

namespace App\Security;

enum Acl
{
    case ROLE_ADMIN;
    case ROLE_USER;
}