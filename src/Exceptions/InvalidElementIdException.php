<?php

namespace Khill\Lavacharts\Exceptions;

use Khill\Lavacharts\Support\Renderable;

class InvalidElementIdException extends RenderingException
{
    public function __construct(Renderable $renderable)
    {
        $message = '"%s" cannot be rendered without an elementId.';

        parent::__construct(sprintf($message, $renderable->getLabel()));
    }
}
