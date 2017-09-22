<?php

namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class NameAvailableException extends ValidationException
{
  public static $defaultTemplates = [
    self::MODE_DEFAULT => [

      self::STANDARD => 'Name is already taken.',

    ],
  ];
}
