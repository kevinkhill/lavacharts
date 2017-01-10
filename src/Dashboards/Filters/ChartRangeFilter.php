<?php

namespace Khill\Lavacharts\Dashboards\Filters;

/**
 * Chart Range Filter Class
 *
 * A slider with two thumbs superimposed onto a chart, to select a range of values
 * from the continuous axis of the chart.
 *
 * @package   Khill\Lavacharts\Dashboards\Filters
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 * @see       https://developers.google.com/chart/interactive/docs/gallery/controls#googlevisualizationchartrangefilter
 */
class ChartRangeFilter extends Filter
{
    /**
     * Type of Filter.
     *
     * @var string
     */
    const TYPE = 'ChartRangeFilter';
}
