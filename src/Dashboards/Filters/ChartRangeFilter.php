<?php

namespace Khill\Lavacharts\Dashboards\Filters;

use \Khill\Lavacharts\Options;

/**
 * Chart Range Filter Class
 *
 * A slider with two thumbs superimposed onto a chart, to select a range of values
 * from the continuous axis of the chart.
 *
 * @package    Khill\Lavacharts
 * @subpackage Dashboards\Filters
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/gallery/controls#googlevisualizationchartrangefilter
 */
class ChartRangeFilter extends Filter
{
    /**
     * Type of Filter.
     *
     * @var string
     */
    const TYPE = 'ChartRangeFilter';

    /**
     * Creates the new Filter object to filter the given column label or index.
     *
     * @param  string|int $columnLabelOrIndex The column label or index to filter.
     * @param  array $config Array of options to set.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct($columnLabelOrIndex, $config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $columnLabelOrIndex, $config);
    }
}
