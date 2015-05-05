<?php namespace Khill\Lavacharts\Charts;

/**
 * AreaChart Class
 *
 * An area chart that is rendered within the browser using SVG or VML. Displays
 * tips when hovering over points.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @since      v1.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use \Khill\Lavacharts\Utils;

class AreaChart extends Chart
{
    use \Khill\Lavacharts\Traits\AxisTitlesPositionTrait;
    use \Khill\Lavacharts\Traits\FocusTargetTrait;
    use \Khill\Lavacharts\Traits\HorizontalAxisTrait;
    use \Khill\Lavacharts\Traits\InterpolateNullsTrait;
    use \Khill\Lavacharts\Traits\LineWidthTrait;
    use \Khill\Lavacharts\Traits\PointSizeTrait;
    use \Khill\Lavacharts\Traits\VerticalAxesTrait;
    use \Khill\Lavacharts\Traits\VerticalAxisTrait;

    public $type = 'AreaChart';

    private $extraOptions = [
        'areaOpacity',
        'axisTitlesPosition',
        'focusTarget',
        'hAxis',
        'isStacked',
        'interpolateNulls',
        'lineWidth',
        'pointSize',
        'vAxes',
        'vAxis'
    ];

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel, $this->extraOptions);
    }

    /**
     * Set the default opacity of the colored area under an area chart series.
     *
     * 0.0 is fully transparent and 1.0 is fully opaque. To specify opacity for
     * an individual series, set the areaOpacity value in the series property.
     *
     * @param  float              $opacity
     * @throws InvalidConfigValue
     * @return AreaChart
     */
    public function areaOpacity($opacity)
    {
        if (Utils::between(0.0, $opacity, 1.0, true) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'float',
                'where 0 < opacity < 1'
            );
        }

        return $this->addOption([__FUNCTION__ => $opacity]);
    }

    /**
     * If set to true, series elements are stacked.
     *
     * @param  bool               $isStacked
     * @throws InvalidConfigValue
     * @return Chart
     */
    public function isStacked($isStacked)
    {
        if (is_bool($isStacked) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this->addOption([__FUNCTION__ => $isStacked]);
    }

}
