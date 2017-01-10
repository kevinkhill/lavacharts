<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Support\Traits\PngRenderableTrait as PngRenderable;

/**
 * HistogramChart Class
 *
 * A histogram is a chart that groups numeric data into bins,
 * displaying the bins as segmented columns. They're used to
 * depict the distribution of a dataset: how often values fall
 * into ranges.
 *
 * Google Charts automatically chooses the number of bins for you.
 * All bins are equal width and have a height proportional to the
 * number of data points in the bin. In other respects, histograms
 * are similar to column charts.
 *
 *
 * @package   Khill\Lavacharts\Charts
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class HistogramChart extends Chart
{
    use PngRenderable;

    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'HistogramChart';

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
    const VISUALIZATION_PACKAGE = 'corechart';

    /**
     * Returns the google javascript package name.
     *
     * @since  3.1.0
     * @return string
     */
    public function getJsClass()
    {
        return 'google.visualization.Histogram';
    }
}
