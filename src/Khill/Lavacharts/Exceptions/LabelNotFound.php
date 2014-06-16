<?php namespace Khill\Lavacharts\Exceptions;

class LabelNotFound extends \Exception
{
    public function __construct($missingLabel, $code = 0)
    {
        $message = 'The chart or datatable labeled "' . $missingLabel . '" was not found.';

        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
