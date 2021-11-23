<?php

namespace App\Events;

use App\Models\Number;

class NumberCreated
{
    public function __construct(public Number $number)
    {
    }
}
