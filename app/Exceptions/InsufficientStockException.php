<?php

namespace App\Exceptions;

class InsufficientStockException extends \Exception
{
    protected $message = 'Insufficient stock';
}
