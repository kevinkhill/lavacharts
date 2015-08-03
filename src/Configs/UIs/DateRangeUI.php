<?php

namespace Khill\Lavacharts\Configs\UIs;
use \Khill\Lavacharts\Configs\Options;

class DateRangeUI extends DataRange
{
    /**
     * Type of UI config object
     *
     * @var string
     */
    const TYPE = 'DateRangeUI';

    /**
     * Builds a new DateRangeUI object.
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
