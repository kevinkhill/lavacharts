<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Support\Traits\PngRenderableTrait as PngRenderable;

/**
 * AreaChart Class
 *
 * An area chart that is rendered within the browser using SVG or VML. Displays
 * tips when hovering over points.
 *
 *
 * @package   Khill\Lavacharts\Charts
 * @since     1.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class AreaChart extends Chart
{
    use PngRenderable;

    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'AreaChart';

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
