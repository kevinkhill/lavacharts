<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidRenderable extends LavaException
{
    public function __construct($badRenderable)
    {
        parent::__construct(sprintf(
            '"%s" is not a valid type of Renderable.', $badRenderable
        ));
    }
}
