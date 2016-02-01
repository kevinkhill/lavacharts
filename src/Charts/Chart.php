<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\Configs\EventManager;
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
class Chart
{
    /**
     * The chart's unique label.
     *
     * @var \Khill\Lavacharts\Values\Label
     */
    protected $label;

    /**
     * The chart's unique label.
     *
     * @var \Khill\Lavacharts\Values\Label
     */
    protected $elementID;

    /**
     * Holds all the customizations for a chart.
     *
     * @var \Khill\Lavacharts\Options
     */
    protected $options;

    /**
     * Datatable for the chart.
     *
     * @var \Khill\Lavacharts\DataTables\DataTable
     */
    protected $datatable;

    /**
     * Enabled chart events with callbacks.
     *
     * @var \Khill\Lavacharts\Configs\EventManager
     */
    protected $events;

    /**
     * Builds a new chart with the given label.
     *
     * @param  \Khill\Lavacharts\Values\Label         $chartLabel Identifying label for the chart.
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable DataTable used for the chart.
     * @param  \Khill\Lavacharts\Options              $options Options fot the chart.
     * @param  array                                  $config Array of options to set on the chart.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct(Label $chartLabel, DataTable $datatable, $config = [])
    {
        $this->label     = $chartLabel;
        $this->datatable = $datatable;
        $this->events    = new EventManager;
        $this->options   = new Options($config);
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
     * @since  3.0.1
     * @param  string $method The method that was called.
     * @param  mixed  $arg    The argument to the method.
     */
    public function __call($method, $arg)
    {
        $this->options->set($method, $arg);

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
     * Returns the chart label.
     *
     * @access public
     * @since  3.0.0
     * @return \Khill\Lavacharts\Values\Label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Retrieves the events if any have been assigned to the chart.
     *
     * @access public
     * @return \Khill\Lavacharts\Configs\EventManager
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Retrieves the Options object from the chart.
     *
     * @access public
     * @since  3.0.1
     * @return \Khill\Lavacharts\Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns the DataTable
     *
     * @access public
     * @since  3.0.0
     * @return \Khill\Lavacharts\DataTables\DataTable
     * @throws \Khill\Lavacharts\Exceptions\DataTableNotFound
     */
    public function getDataTable()
    {
        if (is_null($this->datatable)) {
            throw new DataTableNotFound($this);
        }

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
        return json_encode($this->getDataTable());
    }

    /**
     * Checks if any events have been assigned to the chart.
     *
     * @access public
     * @return bool
     */
    public function hasEvents()
    {
        return $this->events->hasEvents();
    }

    /**
     * Register javascript callbacks for specific events.
     *
     * Set with an associative array where the keys are events and the values are the
     * javascript callback functions.
     *
     * Valid events are:
     * [ animationfinish | error | onmouseover | onmouseout | ready | select | statechange ]
     *
     * @access public
     * @param  array $events Array of events associated to a callback
     * @return \Khill\Lavacharts\Charts\Chart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function events($events)
    {
        if (is_array($events) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'array',
                'who\'s keys are one of '.Utils::arrayToPipedString($this->defaultEvents)
            );
        }

        foreach ($events as $event => $callback) {
            if (Utils::nonEmptyString($callback) === false) {
                throw new InvalidConfigValue(
                    static::TYPE . '->' . __FUNCTION__,
                    'string'
                );
            }

            $this->events->set($event, $callback);
        }

        return $this;
    }

    /**
     * Assigns a datatable to use for the Chart.
     *
     * @deprecated Apply the DataTable to the chart in the constructor.
     * @access public
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable
     * @return \Khill\Lavacharts\Charts\Chart
     */
    public function datatable(DataTable $datatable)
    {
        $this->datatable = $datatable;

        return $this;
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
