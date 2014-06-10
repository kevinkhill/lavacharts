<?php namespace Khill\Lavacharts\Exceptions;

class InvalidConfigProperty extends \Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($class, $function, $rejectedProp, $acceptedProps, $code = 0)
    {
        $message  = '"'.$rejectedProp.'" is not a valid property for ' . $class . '->' . $function . ', ';
    	$message .= 'must be one of [ ';

        natcasesort($acceptedProps);

        foreach ($acceptedProps as $prop)
        {
            $message .= $prop . ' | ';
        }

        $message = substr_replace($message, "", -2) . ']';

        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}
