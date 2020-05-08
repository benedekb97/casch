<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class IDontGiveAFuckException extends Exception
{
    public function __construct($message = "I don't give a fuck!", $code = 418, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
