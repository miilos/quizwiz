<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthException extends \Exception
{
    public function __construct($message = "", $code = Response::HTTP_UNAUTHORIZED, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
