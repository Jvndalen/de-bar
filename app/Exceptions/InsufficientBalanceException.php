<?php

namespace App\Exceptions;

class InsufficientBalanceException extends \Exception
{
    protected $message = 'Insufficient balance';
}
