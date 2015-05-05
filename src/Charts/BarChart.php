<?php namespace Khill\Lavacharts\Charts;

/**
 * BarChart Class
 *
 * A vertical bar chart that is rendered within the browser using SVG or VML.
 * Displays tips when hovering over bars. For a horizontal version of this
 * chart, see the Bar Chart.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @since      v2.3.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Configs\Annotation;
use Khill\Lavacharts\Configs\HorizontalAxis;
use Khill\Lavacharts\Configs\VerticalAxis;

class BarChart extends Chart
{
    use \Khill\Lavacharts\Traits\AnnotationsTrait;
    use \Khill\Lavacharts\Traits\AxisTitlesPositionTrait;
    use \Khill\Lavacharts\Traits\FocusTargetTrait;
    use \Khill\Lavacharts\Traits\HorizontalAxesTrait;
    use \Khill\Lavacharts\Traits\HorizontalAxisTrait;
    use \Khill\Lavacharts\Traits\SeriesTrait;
    use \Khill\Lavacharts\Traits\VerticalAxesTrait;
    use \Khill\Lavacharts\Traits\VerticalAxisTrait;

    public $type = 'BarChart';

    private $extraOptions = [
        'annotations',
        'axisTitlesPosition',
        'barGroupWidth',
        //'bars',
        //'chart.subtitle',
        //'chart.title',
        'dataOpacity',
        'enableInteractivity',
        'focusTarget',
        'forceIFrame',
        'hAxes',
        'hAxis',
        'isStacked',
        'reverseCategories',
        'orientation',
        'series',
        'theme',
        'trendlines',
        'trendlines.n.color',
        'trendlines.n.degree',
        'trendlines.n.labelInLegend',
        'trendlines.n.lineWidth',
        'trendlines.n.opacity',
        'trendlines.n.pointSize',
        'trendlines.n.showR2',
        'trendlines.n.type',
        'trendlines.n.visibleInLegend',
        'vAxis',
        'vAxis'
    ];

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel, $this->extraOptions);
    }

    /**
     * The width of a group of bars, specified in either of these formats:
     * - Pixels (e.g. 50).
     * - Percentage of the available width for each group (e.g. '20%'),
     *   where '100%' means that groups have no space between them.
     *
     * @param  mixed    $barGroupWidth
     * @return BarChart
     */
    public function barGroupWidth($barGroupWidth)
    {
        if (Utils::isIntOrPercent($barGroupWidth) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string | int',
                'must be a valid int or percent [ 50 | 65% ]'
            );
        }

        return $this->addOption(['bar' => ['groupWidth' => $barGroupWidth]]);
    }

    /**
     * The transparency of data points, with 1.0 being completely opaque and 0.0 fully transparent.
     *
     * @param  float    $dataOpacity
     * @return BarChart
     */
    public function dataOpacity($dataOpacity)
    {
        if (Utils::between(0.0, $dataOpacity, 1.0) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'float',
                'between 0.0 - 1.0'
            );
        }

        return $this->addOption([__FUNCTION__ => $dataOpacity]);
    }

    /**
     * Whether the chart throws user-based events or reacts to user interaction.
     *
     * If false, the chart will not throw 'select' or other interaction-based events
     * (but will throw ready or error events), and will not display hovertext or
     * otherwise change depending on user input.
     *
     * @param  bool     $enableInteractivity
     * @return BarChart
     */
    public function enableInteractivity($enableInteractivity)
    {
        if (is_bool($enableInteractivity) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this->addOption([__FUNCTION__ => $enableInteractivity]);
    }

    /**
     * Draws the chart inside an inline frame.
     *
     * Note that on IE8, this option is ignored; all IE8 charts are drawn in i-frames.
     *
     * @param bool $iframe
     *
     * @return BarChart
     */
    public function forceIFrame($iframe)
    {
        if (is_bool($iframe)) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this->addOption([__FUNCTION__ => $iframe]);
    }

    /**
     * 
     * 
     * @param  bool     $orientation
     * @return BarChart 
     */
    public function orientation($orientation)
    {
        $values = [
            'horizontal',
            'vertical'
        ];

        if (in_array($orientation, $values, true) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'must be one of '.Utils::arrayToPipedString($values)
            );
        }

        return $this->addOption([__FUNCTION__ => $orientation]);
    }

    /**
     * If set to true, series elements are stacked.
     *
     * @param  bool     $isStacked
     * @return BarChart
     */
    public function isStacked($isStacked)
    {
        if (is_bool($isStacked) === false) {
            
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this->addOption([__FUNCTION__ => $isStacked]);
    }

    /**
     * If set to true, will draw series from bottom to top. The default is to draw top-to-bottom.
     *
     * @param  bool               $reverseCategories
     * @throws InvalidConfigValue
     * @return BarChart
     */
    public function reverseCategories($reverseCategories)
    {
        if (is_bool($reverseCategories) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this->addOption([__FUNCTION__ => $reverseCategories]);
    }

    /**
     * A theme is a set of predefined option values that work together to achieve a specific chart
     * behavior or visual effect. Currently only one theme is available:
     *  'maximized' - Maximizes the area of the chart, and draws the legend and all of the
     *                labels inside the chart area. Sets the following options:
     *
     * chartArea: {width: '100%', height: '100%'},
     * legend: {position: 'in'},
     * titlePosition: 'in', axisTitlesPosition: 'in',
     * hAxis: {textPosition: 'in'}, vAxis: {textPosition: 'in'}
     *
     * @param  string   $theme
     * @return BarChart
     */
    public function theme($theme)
    {
        $values = array(
            'maximized'
        );

        if (in_array($theme, $values, true) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'must be one of '.Utils::arrayToPipedString($values)
            );
        }

        return $this->addOption([__FUNCTION__ => $theme]);
    }
}
