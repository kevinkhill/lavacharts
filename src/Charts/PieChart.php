<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Support\Traits\PngRenderableTrait as PngRenderable;

/**
 * PieChart Class
 *
 * A pie chart that is rendered within the browser using SVG or VML. Displays
 * tooltips when hovering over slices.
 *
 *
 * @package   Khill\Lavacharts\Charts
 * @since     1.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class PieChart extends Chart
{
    use PngRenderable;
}
