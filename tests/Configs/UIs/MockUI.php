<?php

namespace Khill\Lavacharts\Tests\Configs\UIs;

use Khill\Lavacharts\Options;
use Khill\Lavacharts\Configs\UIs\UI;

class MockUI extends UI
{
    const TYPE = 'MockUI';

    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }
}