<?php

namespace Newsapp\Exceptions;

/**
 * Custom Exception class thown when the user isn't athorize to access an action
 */
class AccessDeniedException extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
