<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class ChartRangeUI extends UI
{
    /**
     * Default options available.
     *
     * @var array
     */
    private $extDefaults = [
        'chartType',
        'chartOptions',
        'chartView',
        'minRangeSize',
        'snapToData'
    ];

    public function __construct($config = [])
    {
        $options = new Options($this->defaults);
        $options->extend($this->extDefaults);
        $options->remove([
            'label',
            'labelSeparator',
            'labelStacking',
            'cssClass'
        ]);

        parent::__construct($options, $config);
    }

    /**
     *
     */
    public function chartType()
    {

    }

    /**
     *
     */
    public function chartOptions()
    {

    }

    /**
     *
     */
    public function chartView()
    {

    }

    /**
     *
     */
    public function minRangeSize()
    {

    }

    /**
     *
     */
    public function snapToData()
    {

    }

    /**
     *
     */
}
