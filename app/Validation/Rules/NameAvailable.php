<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use App\Models\CallCountReport;
/**
 * Check if name exist
 */
class NameAvailable extends AbstractRule
{

  public function validate($input)
  {
      return CallCountReport::where('name', $input)->count() === 0;
  }

}
