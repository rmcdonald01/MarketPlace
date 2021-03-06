<?php

namespace App\Validation\Rules;

use App\Models\User;
use Respect\Validation\Rules\AbstractRule;

class MinPayment extends AbstractRule
{
    public function validate($input)
    {
        return $input > 10;
    }
}
