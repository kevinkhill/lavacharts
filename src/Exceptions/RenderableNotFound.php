<?php

namespace Khill\Lavacharts\Exceptions;

class RenderableNotFound extends LavaException
{
    public function __construct($renderable)
    {
        $message = 'Renderable not found. The label "%s" does not exist in the Volcano.';

        parent::__construct(sprintf($message, $renderable));
    }
}
