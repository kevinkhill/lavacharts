<?php namespace Khill\Lavacharts;

/**
 * Lavacharts - A PHP wrapper library for the Google Chart API
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license   http://opensource.org/licenses/GPL-3.0 GPLv3
 */

use Khill\Lavacharts\Configs\DataTable;
use Khill\Lavacharts\Exceptions\LabelNotFound;
use Khill\Lavacharts\Exceptions\InvalidLavaObject;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;
use Khill\Lavacharts\Exceptions\InvalidConfigProperty;

class Lavacharts
{
    /**
     * @var Khill\Lavacharts\Volcano Holds all of the defined Charts and DataTables.
     */
    protected $volcano = null;

    /**
     * @var array Lavachart configuration options.
     */
    protected $config = array();

    /**
     * @var string Lavacharts root namespace
     */
    protected $rootSpace = 'Khill\\Lavacharts\\';

    /**
     * @var boolean Status of chart generation errors.
     */
    protected $hasError = false;

    /**
     * @var array Array containing the list of errors.
     */
    protected $errorLog = array();

    /**
     * @var array Types of charts that can be created.
     */
    protected $chartClasses = array(
        'DataTable',
        'LineChart',
        'AreaChart',
        'PieChart',
        'DonutChart',
        'ColumnChart',
        'GeoChart',
        'ComboChart' // @TODO WIP
    );

    /**
     * @var array Holds all of the defined configuration class names.
     */
    protected $configClasses = array(
        'ConfigOptions',
        'Annotation',
        'Axis',
//        'DataCell',
        'BoxStyle',
        'BackgroundColor',
        'ChartArea',
        'ColorAxis',
        'HorizontalAxis',
        'JsDate',
        'Gradient',
        'Legend',
        'MagnifyingGlass',
        'TextStyle',
        'Tooltip',
        'Series', // @TODO WIP
        'SizeAxis',
        'Slice',
        'VerticalAxis'
    );

    /**
     * @var array Acceptable global configuration options.
     */
    protected $validGlobals = array(
        'errorWrap',
        'errorClass',
        'textStyle'
    );

    /**
     * Starts up the Lavachart master object.
     */
    public function __construct()
    {
        $this->volcano = new Volcano();
    }

    /**
     * Magic function to reduce repetitive coding and create aliases.
     *
     * @access public
     *
     * @param string $member Name of method
     * @param array $arguments Passed arguments
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidLavaObject
     * @return mixed Returns Charts, DataTables, and Config Objects
     */
    public function __call($member, $arguments)
    {
        if ($member == 'DataTable') {
            return $this->dataTableFactory($arguments[0]);

        } elseif (in_array($member, $this->chartClasses)) {
            return $this->chartFactory($member, $arguments[0]);

        } elseif (in_array($member, $this->configClasses)) {
            return $this->configFactory($member, $arguments[0]);

        } else {
            throw new InvalidLavaObject($member);
        }
    }

    /**
     * Creates and stores DataTables
     *
     * If there is no label, then the DataTable is just returned.
     * If there is a label, the DataTable is stored within the Volcano,
     * accessable via a call to the type of object, with the label
     * as the paramater.
     *
     * @access private
     *
     * @param  string $label Label applied to the datatable.
     *
     * @return Khill\Lavachart\DataTable
     */
    private function dataTableFactory($label = '')
    {
        if (empty($label)) {
            return new DataTable();
        } else {
            try {
                return $this->volcano->getDataTable($label);
            } catch (LabelNotFound $e) {
                $this->volcano->storeDataTable(new DataTable(), $label);

                return $this->volcano->getDataTable($label);
            }
        }
    }

    /**
     * Creates and stores Charts
     *
     * If there is no label, then the Chart is just returned.
     * If there is a label, the Chart is stored within the Volcano,
     * accessable via a call to the type of object, with the label
     * as the paramater.
     *
     * @access private
     *
     * @param  string $label Label applied to the chart.
     *
     * @return Khill\Lavachart\Chart
     */
    private function chartFactory($type, $label = '')
    {
        $chartObj = $this->rootSpace . 'Charts\\' . $type;

        if (class_exists($chartObj)) {
            try {
                return $this->volcano->getChart($label);
            } catch (LabelNotFound $e) {
                $this->volcano->storeChart(new $chartObj($this->volcano, $label), $label);

                return $this->volcano->getChart($label);
            }
        } else {
            throw new InvalidLavaObject($type);
        }
    }

    /**
     * Creates ConfigObjects
     *
     * @access private
     *
     * @param  string $type Type of configObject to create.
     * @param  array $options Array of options to pass to the config object.
     *
     * @return Khill\Lavachart\Configs\ConfigObject
     */
    private function configFactory($type, $options = array())
    {
        $configObj = $this->rootSpace . 'Configs\\' . $type;

        if (class_exists($configObj)) {
            return new $configObj($options);
        } else {
            throw new InvalidLavaObject($type);
        }
    }

    /**
     * Creates configuration objects to save a step instansiating and allow for
     * chaining directly from creation.
     *
     * @access private
     *
     * @param  string $configObject
     * @param  array $options
     *
     * @return Khill\Lavacharts\Configs\ConfigOptions configuration object
     */
    private function configObjectFactory($configObject, $options)
    {
        if ($configObject == 'JsDate') {
            $jsDate = new JsDate();

            return $jsDate->parse($options);
        } else {
            $class = $this->rootSpace.'Configs\\'.$configObject;

            return empty($options[0]) ? new $class() : new $class($options[0]);
        }
    }

    /**
     * Sets global configuration options for the whole Lavachart library.
     *
     * Accepted config options include:
     * errorPrepend: An html string
     *
     * @access public
     *
     * @param array $config Array of configurations options
     *
     * @return Khill\Lavachart
     */
    public function setGlobals($config)
    {
        if (is_array($config)) {
            foreach ($config as $option => $value) {
                if (in_array($option, $this->validGlobals)) {
                    $this->config[$option] = $value;
                } else {
                    throw new InvalidConfigProperty(
                        __METHOD__,
                        $option,
                        $this->validGlobals
                    );
                }
            }
        } else {
            throw new InvalidConfigValue(
                get_class(),
                __METHOD__,
                'array'
            );
        }
    }

    /**
     * Builds a div html element for a chart to be rendered into.
     *
     * Calling with no arguments will return a div with the ID set to what was
     * given to the outputInto() function.
     *
     * Passing two (int)s will set the width and height respectivly and the div
     * ID will be set via the string given in the outputInto() function.
     *
     *
     * This is useful for the AnnotatedTimeLine Chart since it MUST have explicitly
     * defined dimensions of the div it is rendered into.
     *
     * The other charts do not require height and width, but do require an ID of
     * the div that will be receiving the chart.
     *
     * @access public
     *
     * @param int $width Width of the containing div (optional).
     * @param int $height Height of the containing div (optional).
     * @throws Khill\Lavacharts\Exceptions\InvalidElementId
     *
     * @return string HTML div element.
     */
    public function div($elementId = '', $width = 0, $height = 0)
    {
        if (is_string($elementId) && ! empty($elementId)) {
            if ($width == 0 || $height == 0) {
                return sprintf('<div id="%s"></div>', $elementId);
            } else {
                if ((is_int($width) && $width > 0) && (is_int($height) && $height > 0)) {
                        return sprintf(
                            '<div id="%s" style="width:%spx; height:%spx;"></div>',
                            $elementId,
                            $width,
                            $height
                        );
                } else {
                    throw new InvalidConfigValue(
                        get_class(),
                        __METHOD__,
                        'int',
                        'greater than 0'
                    );
                }
            }
        } else {
            throw new InvalidElementId($elementId);
        }
    }
}
