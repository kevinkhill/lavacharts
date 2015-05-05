<?php namespace Khill\Lavacharts\Charts;

/**
 * Combo Chart Class
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
 * @since      v2.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use \Khill\Lavacharts\Utils;

class ComboChart extends Chart
{
    use \Khill\Lavacharts\Traits\AnnotationsTrait;
    use \Khill\Lavacharts\Traits\AreaOpacityTrait;
    use \Khill\Lavacharts\Traits\AxisTitlesPositionTrait;
    use \Khill\Lavacharts\Traits\BarGroupWidthTrait;
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
    use \Khill\Lavacharts\Traits\ReverseCategoriesTrait;
    use \Khill\Lavacharts\Traits\SeriesTrait;
    use \Khill\Lavacharts\Traits\ThemeTrait;
    use \Khill\Lavacharts\Traits\VerticalAxesTrait;
    use \Khill\Lavacharts\Traits\VerticalAxisTrait;

    public $type = 'ComboChart';

    private $extraOptions = [
        'annotations',
        'areaOpacity',
        'axisTitlesPosition',
        'barGroupWidth',
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
        'reverseCategories',
        'selectionMode',
        'series',
        'seriesType',
        'theme',
        'vAxes',
        'vAxis'
    ];

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel, $this->extraOptions);
    }    

    /**
     * When selectionMode is 'multiple', users may select multiple data points.
     *
     * @param  string $selectionMode
     * @return Chart
     */
    public function selectionMode($selectionMode)
    {
        $values = [
            'multiple',
            'single'
        ];

        if (in_array($selectionMode, $values, true) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'must be one of '.Utils::arrayToPipedString($values)
            );
        }

        return $this->addOption([__FUNCTION__ => $selectionMode]);
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
crosshair - object - null - [[An object containing the crosshair properties for the chart.]]
crosshair.color - string - default - [[The crosshair color, expressed as either a color name (e.g., "blue") or an RGB value (e.g., "#adf").]]
crosshair.focused - object - default - [[An object containing the crosshair properties upon focus.Example: crosshair: { focused: { color: '#3bc', opacity: 0.8 } }]]
crosshair.opacity - number - 1.0 - [[The crosshair opacity, with 0.0 being fully transparent and 1.0 fully opaque.]]
crosshair.orientation - string - 'both' - [[The crosshair orientation, which can be 'vertical' for vertical hairs only, 'horizontal' for horizontal hairs only, or 'both' for traditional crosshairs.]]
crosshair.selected - object - default - [[An object containing the crosshair properties upon selection.Example: crosshair: { selected: { color: '#3bc', opacity: 0.8 } }]]
crosshair.trigger - string - 'both' - [[When to display crosshairs: on 'focus', 'selection', or 'both'.]]


pointShape - string - 'circle' - [[The shape of individual data elements: 'circle', 'triangle', 'square', 'diamond', 'star', or 'polygon'. See the points documentation for examples.]]
pointSize - number - 0 - [[Diameter of displayed points in pixels. Use zero to hide all points.
     You can override values for individual series using the
    series property.]]


*/
