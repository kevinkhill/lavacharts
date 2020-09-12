<?php

namespace Khill\Lavacharts\Support\Traits;

/**
 * Trait PngRenderableTrait
 *
 * When applied to a Chart, it will enable the output of the Chart as a PNG vs SVG
 *
 * @package   Khill\Lavacharts\Support\Traits
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
trait PngRenderableTrait
{
    /**
     * Chart output override.
     *
     * @var bool
     */
    protected $png = false;

    /**
     * Sets the chart to be output as a PNG instead of SVG.
     *
     * @param bool $png Sets the chart output override status.
     */
    public function setPngOutput($png)
    {
        $this->png = $png;
    }

    /**
     * Gets the chart output override status.
     *
     * @return bool Returns the output override status.
     */
    public function getPngOutput()
    {
        return $this->png;
    }
}
