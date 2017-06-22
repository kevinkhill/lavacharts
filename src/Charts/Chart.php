<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Values\ElementId;
use Khill\Lavacharts\Values\Label;
use Khill\Lavacharts\Support\Customizable;
use Khill\Lavacharts\Support\Contracts\DataTable;
use Khill\Lavacharts\Support\Contracts\Jsonable;
use Khill\Lavacharts\Support\Contracts\JsPackage;
use Khill\Lavacharts\Support\Contracts\Renderable;
use Khill\Lavacharts\Support\Contracts\Wrappable;
use Khill\Lavacharts\Support\Traits\HasDataTableTrait as HasDataTable;
use Khill\Lavacharts\Support\Traits\RenderableTrait as IsRenderable;

/**
 * Class Chart
 *
 * Parent to all charts which has common properties and methods
 * used between all the different charts.
 *
 *
 * @package   Khill\Lavacharts\Charts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Chart extends Customizable implements DataTable, Renderable, Wrappable, Jsonable, JsPackage
{
    use HasDataTable, IsRenderable;

    /**
     * Type of wrappable class
     */
    const WRAP_TYPE = 'chartType';

    /**
     * Builds a new chart with the given label.
     *
     * @param \Khill\Lavacharts\Values\Label         $chartLabel Identifying label for the chart.
     * @param \Khill\Lavacharts\DataTables\DataTable $datatable DataTable used for the chart.
     * @param array                                  $options Options fot the chart.
     */
    public function __construct(Label $chartLabel, DataTable $datatable = null, array $options = [])
    {
        parent::__construct($options);

        $this->label = $chartLabel;
        $this->datatable = $datatable->getDataTable();

        if (array_key_exists('elementId', $options)) {
            $this->elementId = new ElementId($options['elementId']);
        }
    }

    /**
     * Returns the chart type.
     *
     * @since  3.0.0
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * Returns the Filter wrap type.
     *
     * @since  3.0.5
     * @return string
     */
    public function getWrapType()
    {
        return static::WRAP_TYPE;
    }

    /**
     * Returns the chart version.
     *
     * @since  3.0.5
     * @return string
     */
    public function getVersion()
    {
        return static::VERSION;
    }

    /**
     * Returns the chart visualization class.
     *
     * @since  3.0.5
     * @return string
     */
    public function getJsPackage()
    {
        return static::VISUALIZATION_PACKAGE;
    }

    /**
     * Returns the chart visualization package.
     *
     * @since  3.0.5
     * @return string
     */
    public function getJsClass()
    {
        return 'google.visualization.' . static::TYPE;
    }

    /**
     * Return a JSON representation of the chart, which would be the customizations.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * Retrieves the events if any have been assigned to the chart.
     *
     * @since  3.0.5
     * @return array
     */
    public function getEvents()
    {
        return $this['events'];
    }

    /**
     * Checks if any events have been assigned to the chart.
     *
     * @return bool
     */
    public function hasEvents()
    {
        return isset($this['events']);
    }

    /**
     * Sets any configuration option, with no checks for type / validity
     *
     *
     * This is method was added in 2.5 as a bandaid to remove the handcuffs from
     * users who want to add options that Google has added, that I have not.
     * I didn't intend to restrict the user to only select options, as the
     * goal was to type isNonEmpty and validate. This method can be used to set
     * any option, just pass in arrays with key value pairs for any setting.
     *
     * If the setting is an object, per the google docs, then use multi-dimensional
     * arrays and they will be converted upon rendering.
     *
     * @since  3.0.0
     * @param  array $options Array of customization options for the chart
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function customize(array $options)
    {
        $this->setOptions($options);

        return $this;
    }
}
