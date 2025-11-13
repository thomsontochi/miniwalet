<?php

namespace App\Exceptions;

use Exception;

class InsufficientFundsException extends Exception
{
    public function __construct()
    {
        parent::__construct('Insufficient funds to complete this transfer.');
    }
}
