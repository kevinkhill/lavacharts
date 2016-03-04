<?php

namespace Khill\Lavacharts\Support\Contracts;

interface WrappableInterface
{
    /**
     * Returns the wrap type, either Control or Chart.
     *
     * @return string
     */
    public function getWrapType();
}
