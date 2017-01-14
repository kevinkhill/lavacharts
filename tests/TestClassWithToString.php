<?php
/**
 * Created by elpiel.
 * Project: lavacharts
 * Date: 14/01/17
 */

namespace Khill\Lavacharts\Tests;


class TestClassWithToString
{

    /**
     * @var string
     */
    private $string;

    /**
     * TestClassWithToString constructor.
     *
     * @param string $string
     */
    public function __construct($string = 'default')
    {

        $this->string = $string;
    }

    public function __toString()
    {
        return $this->string;
    }
}