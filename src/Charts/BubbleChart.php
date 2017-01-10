<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Support\Traits\PngRenderableTrait as PngRenderable;

/**
 * BubbleChart Class
 *
 * A bubble chart is used to visualize a data set with two to four dimensions.
 * The first two dimensions are visualized as coordinates, the third as color
 * and the fourth as size.
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
class BubbleChart extends Chart
{
    use PngRenderable;

    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'BubbleChart';

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
}
