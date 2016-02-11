<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\Configs\Renderable;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Exceptions\DataTableNotFound;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * Chart Class, Parent to all charts.
 *
 * Has common properties and methods used between all the different charts.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Chart extends Renderable
{
    use \Khill\Lavacharts\Traits\OptionsTrait;
    use \Khill\Lavacharts\Traits\LabelTrait;

    /**
     * Datatable for the chart.
     *
     * @var \Khill\Lavacharts\DataTables\DataTable
     */
    protected $datatable;

    /**
     * Builds a new chart with the given label.
     *
     * @param  \Khill\Lavacharts\Values\Label         $chartLabel Identifying label for the chart.
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable DataTable used for the chart.
     * @param  \Khill\Lavacharts\Configs\Options      $options Options fot the chart.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct(
        Label $chartLabel,
        DataTable $datatable,
        Options $options = null,
        ElementId $elementId = null
    ) {
        $this->label     = $chartLabel;
        $this->datatable = $datatable;
        $this->options   = $options;

        parent::__construct($elementId);
    }

    /**
     * This method will map any method calls for setting options.
     *
     *
     * Before 3.0, methods could be used as well as config arrays for
     * setting options on a chart. This will prevent BC breaks to anyone
     * who upgrades to 3.0 but still has 2.5 syntax.
     *
     * @access public
     * @since  3.1.0
     * @param  string $method The method that was called.
     * @param  mixed  $arg    The argument to the method.
     */
    public function __call($method, $arg)
    {
        $this->options[$method] = $arg;

        return $this;
    }

    /**
     * Returns the chart type.
     *
     * @since 3.0.0
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
    }


    /**
     * Returns the DataTable
     *
     * @access public
     * @since  3.0.0
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public function getDataTable()
    {
        return $this->datatable;
    }

    /**
     * Returns a JSON string representation of the datatable.
     *
     * @access public
     * @since  2.5.0
     * @throws \Khill\Lavacharts\Exceptions\DataTableNotFound
     * @return string
     */
    public function getDataTableJson()
    {
        return json_encode($this->datatable);
    }

    /**
     * Retrieves the events if any have been assigned to the chart.
     *
     * @since  3.1.0
     * @return array
     */
    public function getEvents()
    {
        return $this->options['events'];
    }

    /**
     * Checks if any events have been assigned to the chart.
     *
     * @access public
     * @return bool
     */
    public function hasEvents()
    {
        return isset($this->options['events']);
    }

    /**
     * Sets any configuration option, with no checks for type / validity
     *
     *
     * This is method was added in 2.5 as a bandaid to remove the handcuffs from
     * users who want to add options that Google has added, that I have not.
     * I didn't intend to restrict the user to only select options, as the
     * goal was to type check and validate. This method can be used to set
     * any option, just pass in arrays with key value pairs for any setting.
     *
     * If the setting is an object, per the google docs, then use multi-dimensional
     * arrays and they will be converted upon rendering.
     *
     * @access public
     * @since  3.0.0
     * @param  array $optionArray Array of customization options for the chart
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function customize($optionArray)
    {
        return $this->options->setOptions($optionArray, false);
    }
}
