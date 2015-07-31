<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class UI
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
        //Category
        'caption',
        'sortValues',
        'selectedValuesLayout',
        'allowNone',
        'allowMultiple',
        'allowTyping',
        'label',
        'labelSeparator',
        'labelStacking',
        'cssClass',
        //ChartRange
        'chartType',
        'chartOptions',
        'chartView',
        'minRangeSize',
        'snapToData',
        //DateRange
        'format',
        'step',
        'ticks',
        'unitIncrement',
        'blockIncrement',
        'showRangeValues',
        'orientation',
        'label',
        'labelSeparator',
        'labelStacking',
        'cssClass',
        //NumberRange
        'format',
        'step',
        'ticks',
        'unitIncrement',
        'blockIncrement',
        'showRangeValues',
        'orientation',
        'label',
        'labelSeparator',
        'labelStacking',
        'cssClass',
        //String
        'realtimeTrigger',
        'label',
        'labelSeparator',
        'labelStacking',
        'cssClass'
    ];

    public function __construct($config)
    {
        $this->options = new Options($this->defaults);

        $this->parseConfig($config);
    }

    public function parseConfig($config)
    {
        foreach ($config as $option => $value) {
            if ($this->options->has($option) === false) {
                throw new InvalidConfigProperty(
                    static::TYPE,
                    __FUNCTION__,
                    $option,
                    $this->options->toArray()
                );
            }

            call_user_func([$this, $option], $value);
        }
    }
}
