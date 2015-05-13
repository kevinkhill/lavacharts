<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidOption extends \Exception
{
    public function __construct($option, $options)
    {
        $shortest = -1;

        foreach ($options as $word) {
            $lev = levenshtein($option, $word);

            if ($lev <= $shortest || $shortest < 0) {
                $intended = $word;
                $shortest = $lev;
            }
        }

        $message = "'$option' is not a valid option, did you mean '$intended'?";

        parent::__construct($message);
    }
}
