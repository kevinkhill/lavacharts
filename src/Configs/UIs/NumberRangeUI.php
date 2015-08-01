<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class NumberRangeUI extends UI
{
    /**
     * Allowed options to set for the UI.
     *
     * @var \Khill\Lavacharts\Configs\Options
     */
    private $options;

    /**
     * Default options available.
     *
     * @var array
     */
    private $defaults = [
        'format',
        'step',
        'ticks',
        'unitIncrement',
        'blockIncrement',
        'showRangeValues',
        'orientation'
    ];

    public function __construct($config)
    {
        $this->options = new Options(parent::$defaults);
        $this->options->extend($this->defaults);

        parent::__construct($config);
    }

    /**
     * Sets the column formatter.
     *
     * @access public
     * @param  \Khill\Lavacharts\DataTables\Formats\Format
     * @return self
     */
    public function format(Format $format)
    {
        $this->format = $format;

        return $this;
    }
}
