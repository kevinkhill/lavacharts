<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class ChartRangeUI extends UI
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
        'chartType',
        'chartOptions',
        'chartView',
        'minRangeSize',
        'snapToData'
    ];

    public function __construct($config)
    {
        $this->options = new Options(parent::$defaults);
        $this->options->extend($this->defaults);
        $this->options->remove([
            'label',
            'labelSeparator',
            'labelStacking',
            'cssClass'
        ]);

        parent::__construct($config);
    }
}
