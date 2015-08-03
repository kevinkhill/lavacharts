<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\DataTables\Formats\Format;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

class NumberRangeUI extends DataRange
{
    /**
     * Type of UI config object
     *
     * @var string
     */
    const TYPE = 'NumberRangeUI';

    /**
     * Builds a new NumberRangeUI object.
     *
     * @param array $config Array of options to set
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);
        $options->extend($this->extDefaults);

        parent::__construct($options, $config);
    }
}
