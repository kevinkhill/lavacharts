<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Series ConfigObject
 *
 * An object containing all the values for a single series in a multiple data
 * set chart, which can be passed into the series property of the chart's options.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Series extends JsonConfig
{
    /**
     * Common Methods
     */
    use \Khill\Lavacharts\Traits\AreaOpacityTrait;
    use \Khill\Lavacharts\Traits\ColorTrait;
    use \Khill\Lavacharts\Traits\CurveTypeTrait;
    use \Khill\Lavacharts\Traits\LabelInLegendTrait;
    use \Khill\Lavacharts\Traits\LineWidthTrait;
    use \Khill\Lavacharts\Traits\PointShapeTrait;
    use \Khill\Lavacharts\Traits\PointSizeTrait;
    use \Khill\Lavacharts\Traits\VisibleInLegendTrait;

    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'Series';

    /**
     * Default options for Series
     *
     * @var array
     */
    private $defaults = [
        'annotation',
        'areaOpacity',
        'color',
        'curveType',
        'lineWidth',
        'labelInLegend',
        'targetAxisIndex',
        'textStyle',
        'type',
        'visibleInLegend'
    ];

    /**
     * Builds the series object when passed an array of configuration options.
     *
     * @param  array $config Options for the series
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * An object to be applied to annotations for this series.
     *
     * This can be used to control, for instance, the textStyle for the series.
     *
     * @param  array $annotationConfig Style of the series annotations
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function annotation($annotationConfig)
    {
        return $this->setOption(__FUNCTION__, new Annotation($annotationConfig));
    }

    /**
     * Overrides for the global Candlestick falling color options.
     *
     * @param  array $fallingColorConfig
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function fallingColor($fallingColorConfig)
    {
        return $this->setOption(__FUNCTION__, new BackgroundColor($fallingColorConfig));
    }

    public function lineDashStyle($lineDashStyle)
    {
        //TODO: what is this?
    }

    /**
     * Overrides for the global Candlestick color options.
     *
     * @param  array $risingColorConfig
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function risingColor($risingColorConfig)
    {
        return $this->setOption(__FUNCTION__, new BackgroundColor($risingColorConfig));
    }

    /**
     * Which axis to assign this series to.
     *
     * 0 is the default axis, and 1 is the opposite axis.
     *
     * Default value is 0; set to 1 to define a chart where different series
     * are rendered against different axes.
     *
     * At least one series much be allocated to the default axis.
     * You can define a different scale for different axes.
     *
     * @param  int $index
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function targetAxisIndex($index)
    {
        return $this->setIntOption(__FUNCTION__, $index);
    }

    /**
     * The default line type for any series not specified in the series property.
     *
     * Available values are:
     * 'line', 'area', 'bars', 'candlesticks' and 'steppedArea'
     *
     * @param  string $type
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function type($type)
    {
        $values = [
            'line',
            'area',
            'bars',
            'candlesticks',
            'steppedArea'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $type, $values);
    }

    /**
     * An object that specifies the series text style.
     *
     * @deprecated
     * @param  array $textStyleConfig
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function textStyle($textStyleConfig)
    {
        return $this->addOption(__FUNCTION__, new TextStyle($textStyleConfig));
    }

}
