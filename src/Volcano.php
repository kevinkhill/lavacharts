<?php

namespace Khill\Lavacharts;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Dashboards\Dashboard;
use Khill\Lavacharts\Exceptions\RenderableNotFound;
use Khill\Lavacharts\Support\ArrayObject;
use Khill\Lavacharts\Support\Contracts\Jsonable;
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
 * @since     4.0.0 Complete refactoring to handle Renderables directly.
 *                  Added DynamicArrayAccess and IteratorAggregate.
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Volcano extends ArrayObject implements Jsonable
{
    use ArrayToJson;

    /**
     * @var Renderable[]
     */
    public $renderables = [];

    public function getArrayAccessProperty()
    {
        return 'renderables';
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
     * @return Renderable
     */
    public function store(Renderable $renderable)
    {
        $this->renderables[$renderable->getLabel()] = $renderable;

        return $renderable;
    }

    /**
     * Fetches an existing Renderable from the Volcano with no checks.
     *
     * @param  string $label Label of the Renderable.
     * @return Chart|Dashboard|Renderable
     * @throws RenderableNotFound
     */
    public function get($label)
    {
        return $this->renderables[$label];
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
     * Try to fetch a Renderable from the Volcano and throw an exception if not found.
     *
     * @param  string $label Label of the Renderable.
     * @return Chart|Dashboard|Renderable
     * @throws RenderableNotFound
     */
    public function find($label)
    {
        if (! $this->exists($label)) {
            throw new RenderableNotFound($label);
        }

        return $this->get($label);
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
