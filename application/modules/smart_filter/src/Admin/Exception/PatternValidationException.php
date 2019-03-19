<?php namespace smart_filter\src\Admin\Exception;

use Exception;

class PatternValidationException extends Exception
{

    public function __construct($message, $code = 0, Exception $previous = null) {
        Exception::__construct($message, $code, $previous);
    }

}