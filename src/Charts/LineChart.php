<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Support\Google;
use \Khill\Lavacharts\Support\Traits\PngRenderableTrait as PngRenderable;
use \Khill\Lavacharts\Support\Traits\MaterialRenderableTrait as MaterialRenderable;

/**
 * LineChart Class
 *
 * A line chart that is rendered within the browser using SVG or VML. Displays
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
class LineChart extends Chart
{
    use PngRenderable, MaterialRenderable;

    /**
     * @inheritdoc
     */
    public function getJsPackage()
    {
        return $this->material ? 'line' : parent::getJsPackage();
    }

    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return $this->material ? Google::charts('Line') : parent::getJsClass();
    }
}
