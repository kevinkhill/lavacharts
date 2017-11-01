<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Support\Customizable;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Values\ElementId;
use Khill\Lavacharts\Values\Label;
use Khill\Lavacharts\Support\Traits\ElementIdTrait as HasElementId;
use Khill\Lavacharts\Support\Traits\DataTableTrait as HasDataTable;
use Khill\Lavacharts\Support\Traits\RenderableTrait as IsRenderable;
use Khill\Lavacharts\Support\Contracts\JsonableInterface as Jsonable;
use Khill\Lavacharts\Support\Contracts\WrappableInterface as Wrappable;
use Khill\Lavacharts\Support\Contracts\RenderableInterface as Renderable;
use Khill\Lavacharts\Support\Contracts\VisualizationInterface as Visualization;

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
class Chart extends Customizable implements Renderable, Wrappable, Jsonable, Visualization
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
        $this->datatable = $datatable;

        $this->setExtendedAttributes();
    }

    /**
     * Set extended chart attributes from the assigned options, if present.
     *
     * @since 3.1.9
     */
    protected function setExtendedAttributes()
    {
        if (array_key_exists('elementId', $this->options)) {
            $this->setElementId($this->options['elementId']);

            unset($this->options['elementId']);
        }

        if (method_exists($this, 'setPngOutput') &&
            array_key_exists('png', $this->options))
            {
                $this->setPngOutput($this->options['png']);

                unset($this->options['png']);
            }

        if (method_exists($this, 'setMaterialOutput') &&
            array_key_exists('material', $this->options))
            {
                $this->setMaterialOutput($this->options['material']);

                unset($this->options['material']);
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
