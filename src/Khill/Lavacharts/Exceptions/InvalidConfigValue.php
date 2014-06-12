<?php namespace Khill\Lavacharts\Exceptions;

class InvalidConfigValue extends \Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($class, $function, $requiredType, $extra = '', $code = 0)
    {
        $message  = 'The value for ' . $class . '->' . $function . ' ';
        $message .= 'must be of type (' . $requiredType .')';

    	   if ($extra !== '') {
            $message .= ' ' . $extra;
    	   }

        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
