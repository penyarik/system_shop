<?php

namespace App\CustomEntity;

enum Currency: int
{
    case USD = 1;
    case EUR = 2;
    case UAH = 3;
    case GBP = 4;
}