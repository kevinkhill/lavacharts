<?php

namespace Khill\Lavacharts;

use ArrayIterator;
use IteratorAggregate;
use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Dashboards\Dashboard;
use Khill\Lavacharts\Exceptions\RenderableNotFound;
use Khill\Lavacharts\Support\Contracts\ArrayAccess;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Jsonable;
use Khill\Lavacharts\Support\Traits\ArrayAccessTrait as DynamicArrayAccess;
use Khill\Lavacharts\Support\Traits\ArrayToJsonTrait as ArrayToJson;
use Khill\Lavacharts\Support\Renderable;
use Khill\Lavacharts\Support\StringValue as Str;

/**
 * Class Volcano
 *
 * Storage class that holds all defined charts and dashboards.
 *
 * @package   Khill\Lavacharts
 * @since     2.0.0
 * @since     3.2.0 Complete refactoring to handle Renderables directly.
 *                  Added DynamicArrayAccess and IteratorAggregate.
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Volcano implements ArrayAccess, Arrayable, IteratorAggregate, Jsonable
{
    use DynamicArrayAccess, ArrayToJson;

    /**
     * @var Renderable[]
     */
    public $renderables = [];

    /**
     * @inheritdoc
     * @since 3.2.0 Adding dynamic ArrayAccess
     */
    public function getArrayAccessProperty()
    {
        return 'renderables';
    }

    /**
     * Get an iterator for the renderables.
     *
     * @since 3.2.0 Enable the use of foreach on the Volcano
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * Get the volcano as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->renderables;
    }

    /**
     * Stores a Renderable in the Volcano.
     *
     * @param  Renderable $renderable
     * @return self
     */
    public function store(Renderable $renderable)
    {
        $this->renderables[$renderable->getLabel()] = $renderable;

        return $this;
    }

    /**
     * Test if a Renderable exists in the Volcano.
     *
     * @param  string $label Unique identifying label of the Renderable to check.
     * @return bool
     */
    public function exists($label)
    {
        $label = Str::verify($label);

        return array_key_exists($label, $this->renderables);
    }

    /**
     * Fetches an existing Renderable from the Volcano.
     *
     * @param  string $label Label of the Renderable.
     * @return Chart|Dashboard|Renderable
     * @throws RenderableNotFound
     */
    public function get($label)
    {
        if (! $this->exists($label)) {
            throw new RenderableNotFound($label);
        }

        return $this->renderables[$label];
    }

    /**
     * Retrieve all the Charts from the volcano.
     *
     * @return Chart[]
     */
    public function getCharts()
    {
        return array_map(function (Renderable $renderable) {
            if ($renderable instanceof Chart) {
                return $renderable;
            }
        }, $this->renderables);
    }

    /**
     * Retrieve all the Dashboards from the volcano.
     *
     * @return Dashboard[]
     */
    public function getDashboards()
    {
        return array_map(function (Renderable $renderable) {
            if ($renderable instanceof Dashboard) {
                return $renderable;
            }
        }, $this->renderables);
    }
}
