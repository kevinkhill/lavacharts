<?php

namespace Khill\Lavacharts\Support\Traits;

/**
 * Trait MaterialRenderableTrait
 *
 * When applied to a Chart, it will enable the output of the Chart in Material Design
 *
 * @package   Khill\Lavacharts\Support\Traits
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
trait MaterialRenderableTrait
{
    /**
     * Render as material or classic.
     *
     * @var bool
     */
    private $material = false;

    /**
     * Sets the chart to be rendered as material design or classic.
     *
     * @param bool $material Sets the material render status.
     */
    public function setMaterialOutput($material)
    {
        $this->material = $material;
    }

    /**
     * Gets the chart render method.
     *
     * @return bool Returns the material render status.
     */
    public function getMaterialOutput()
    {
        return $this->material;
    }
}
