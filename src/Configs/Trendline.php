<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Trendline ConfigObject
 *
 * An object containing all the values for a single trendline in a multiple data
 * set chart, which can be passed into the trendline property of the chart's options.
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
class Trendline extends JsonConfig
{
    /**
     * Common Methods
     */
    use \Khill\Lavacharts\Traits\ColorTrait;
    use \Khill\Lavacharts\Traits\LabelInLegendTrait;
    use \Khill\Lavacharts\Traits\LineWidthTrait;
    use \Khill\Lavacharts\Traits\OpacityTrait;
    use \Khill\Lavacharts\Traits\PointSizeTrait;
    use \Khill\Lavacharts\Traits\VisibleInLegendTrait;

    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'Trendline';

    /**
     * Default options for Trendline
     *
     * @var array
     */
    private $defaults = [
        'color',
        'degree',
        'labelInLegend',
        'lineWidth',
        'opacity',
        'pointSize',
        'pointsVisible',
        'showR2',
        'type',
        'visibleInLegend',
    ];

    /**
     * Builds the trendline object when passed an array of configuration options.
     *
     * @param  array $config Options for the trendline
     * @return \Khill\Lavacharts\Configs\Trendline
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * Sets the display of trendline points.
     *
     * @param  bool $degree
     * @return \Khill\Lavacharts\Configs\Trendline
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function degree($degree)
    {
        return $this->setIntOption(__FUNCTION__, $degree);
    }

    /**
     * Sets the display of trendline points.
     *
     * @param  bool $visible
     * @return \Khill\Lavacharts\Configs\Trendline
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function pointsVisible($visible)
    {
        return $this->setBoolOption(__FUNCTION__, $visible);
    }

    /**
     * Sets the display of the status of the label in the legend.
     *
     * @param  bool $visible
     * @return \Khill\Lavacharts\Configs\Trendline
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function showR2($visible)
    {
        return $this->setBoolOption(__FUNCTION__, $visible);
    }
    /**
     * The default line type for any series not specified in the series property.
     *
     * Available values are:
     * 'line', 'exponential', 'polynomial'
     *
     * @param  string $type
     * @return \Khill\Lavacharts\Configs\Series
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function type($type)
    {
        $values = [
            'linear',
            'exponential',
            'polynomial'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $type, $values);
    }
}
