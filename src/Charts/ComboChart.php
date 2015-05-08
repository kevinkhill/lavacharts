<?php namespace Khill\Lavacharts\Charts;

/**
 * ComboChart Class
 *
 * A chart that lets you render each series as a different marker type from the following list:
 * line, area, bars, candlesticks and stepped area.
 *
 * To assign a default marker type for series, specify the seriesType property.
 * Use the series property to specify properties of each series individually.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @since      2.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use \Khill\Lavacharts\Utils;

class ComboChart extends Chart
{
    /**
     * Common methods
     */
    use \Khill\Lavacharts\Traits\AnnotationsTrait;
    use \Khill\Lavacharts\Traits\AreaOpacityTrait;
    use \Khill\Lavacharts\Traits\AxisTitlesPositionTrait;
    use \Khill\Lavacharts\Traits\BarGroupWidthTrait;
    use \Khill\Lavacharts\Traits\CrosshairTrait;
    use \Khill\Lavacharts\Traits\CurveTypeTrait;
    use \Khill\Lavacharts\Traits\DataOpacityTrait;
    use \Khill\Lavacharts\Traits\EnableInteractivityTrait;
    use \Khill\Lavacharts\Traits\FocusTargetTrait;
    use \Khill\Lavacharts\Traits\ForceIFrameTrait;
    use \Khill\Lavacharts\Traits\HorizontalAxisTrait;
    use \Khill\Lavacharts\Traits\InterpolateNullsTrait;
    use \Khill\Lavacharts\Traits\IsStackedTrait;
    use \Khill\Lavacharts\Traits\LineWidthTrait;
    use \Khill\Lavacharts\Traits\OrientationTrait;
    use \Khill\Lavacharts\Traits\PointShapeTrait;
    use \Khill\Lavacharts\Traits\PointSizeTrait;
    use \Khill\Lavacharts\Traits\ReverseCategoriesTrait;
    use \Khill\Lavacharts\Traits\SelectionModeTrait;
    use \Khill\Lavacharts\Traits\SeriesTrait;
    use \Khill\Lavacharts\Traits\ThemeTrait;
    use \Khill\Lavacharts\Traits\VerticalAxesTrait;
    use \Khill\Lavacharts\Traits\VerticalAxisTrait;

    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'ComboChart';

    /**
     * Javascript chart version.
     *
     * @var string
     */
    const VERSION = '1';

    /**
     * Javascript chart package.
     *
     * @var string
     */
    const VIZ_PACKAGE = 'corechart';

    /**
     * Javascript chart class.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.ComboChart';

    /**
     * Builds a new chart with the given label.
     *
     * @param  string $chartLabel Identifying label for the chart.
     * @return Chart
     */
    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array_merge([
            'annotations',
            'areaOpacity',
            'axisTitlesPosition',
            'barGroupWidth',
            'crosshair',
            'curveType',
            'dataOpacity',
            'enableInteractivity',
            'focusTarget',
            'forceIFrame',
            'hAxis',
            'interpolateNulls',
            'isStacked',
            'lineWidth',
            'orientation',
            'pointShape',
            'pointSize',
            'reverseCategories',
            'selectionMode',
            'series',
            'seriesType',
            'theme',
            'vAxes',
            'vAxis'
        ], $this->defaults);
    }

    /**
     * The default line type for any series not specified in the series property.
     * Available values are:
     * 'line', 'area', 'bars', 'candlesticks' and 'steppedArea'
     *
     * @param  string             $type
     * @throws InvalidConfigValue
     * @return ComboChart
     */
    public function seriesType($type)
    {
        $values = [
            'line',
            'area',
            'bars',
            'candlesticks',
            'steppedArea'
        ];

        if (in_array($type, $values, true) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this->addOption([__FUNCTION__ => $type]);
    }
}

/*
candlestick.hollowIsRising - bool - false (will later be changed to true) - [[If true, rising candles will appear hollow and falling candles will appear solid, otherwise, the opposite.]]
candlestick.fallingColor.fill - string - auto (depends on the series color and hollowIsRising) - [[The fill color of falling candles, as an HTML color string.]]
candlestick.fallingColor.stroke - string - auto (the series color) - [[The stroke color of falling candles, as an HTML color string.]]
candlestick.fallingColor.strokeWidth - number - 2 - [[The stroke width of falling candles, as an HTML color string.]]
candlestick.risingColor.fill - string - auto (white or the series color, depending on hollowIsRising) - [[The fill color of rising candles, as an HTML color string.]]
candlestick.risingColor.stroke - string - auto (the series color or white, depending on hollowIsRising) - [[The stroke color of rising candles, as an HTML color string.]]
candlestick.risingColor.strokeWidth - number - 2 - [[The stroke width of rising candles, as an HTML color string.]]
*/
