<?php

namespace Khill\Lavacharts\Exceptions;

use Khill\Lavacharts\Support\Renderable;

class ElementIdException extends RenderingException
{
    public function __construct(Renderable $renderable)
    {
        $message = 'Cannot render without an elementId.';

        parent::__construct($renderable, $message);
    }
}
