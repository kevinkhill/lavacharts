<?php namespace Khill\Lavacharts\Exceptions;

class InvalidConfigProperty extends \Exception
{
    public function __construct($class, $function, $rejectedProp, $acceptedProps, $code = 0)
    {
        $message  = '"'.$rejectedProp.'" is not a valid property for ' . $class . '->' . $function . ', ';
        $message .= 'must be one of [ ';

        natcasesort($acceptedProps);

        foreach ($acceptedProps as $prop) {
            $message .= $prop . ' | ';
        }

        $message = substr_replace($message, "", -2) . ']';

        parent::__construct($message, $code);
    }
}
