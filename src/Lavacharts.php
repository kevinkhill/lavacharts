<?php

namespace Khill\Lavacharts;

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\Dashboards\Dashboard;
use Khill\Lavacharts\Dashboards\DashboardFactory;
use Khill\Lavacharts\Dashboards\Filters\Filter;
use Khill\Lavacharts\Dashboards\Filters\FilterFactory;
use Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper;
use Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper;
use Khill\Lavacharts\DataTables\DataFactory;
use Khill\Lavacharts\DataTables\Columns\Format;
use Khill\Lavacharts\Exceptions\InvalidFormatType;
use Khill\Lavacharts\Exceptions\InvalidLabelException;
use Khill\Lavacharts\Javascript\ScriptManager;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Contracts\DataInterface;
use Khill\Lavacharts\Support\Contracts\Jsonable;
use Khill\Lavacharts\Support\Psr4Autoloader;
use Khill\Lavacharts\Support\Renderable;
use Khill\Lavacharts\Support\StringValue as Str;
use Khill\Lavacharts\Support\Traits\ArrayToJsonTrait as ArrayToJson;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;

/**
 * Lavacharts - A PHP wrapper library for the Google Chart API
 *
 *
 * @package       Khill\Lavacharts
 * @since         1.0.0
 * @author        Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 *
 * @method store
 * @method exists
 * @method get
 * @method getCharts
 * @method getDashboards
 * @method DataTable
 * @method JoinedDataTable
 * @method DataFactory
 * @method ArrowFormat
 * @method BarFormat
 * @method DateFormat
 * @method NumberFormat
 */
class Lavacharts implements Customizable, Jsonable, Arrayable
{
    use HasOptions, ArrayToJson;

    /**
     * Lavacharts version
     */
    const VERSION = '4.0.0';

    /**
     * Volcano methods to map with __call()
     */
    const VOLCANO_METHODS = [
        'store',
        'exists',
        'get',
        'getCharts',
        'getDashboards',
    ];

    /**
     * DataTable types to map with __call()
     */
    const DATATABLE_TYPES = [
        'DataTable',
        'JoinedDataTable',
    ];

    /**
     * Base classes to map with __call()
     */
    const BASE_LAVA_CLASSES = [
//        'ChartWrapper',
//        'ControlWrapper',
//        'DataTable',
        'DataFactory', //TODO: look into this again.
    ];

    /**
     * Storage for all of the defined Renderables.
     *
     * @var Volcano
     */
    private $volcano;

    /**
     * Instance of the ScriptManager.
     *
     * @var ScriptManager
     */
    private $scriptManager;

    /**
     * Lavacharts constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->initOptions($options);

        if (! $this->usingComposer()) {
            require_once(__DIR__ . '/Support/Psr4Autoloader.php');

            $loader = new Psr4Autoloader;
            $loader->register();
            $loader->addNamespace('Khill\Lavacharts', __DIR__);
        }

        $this->volcano       = new Volcano;
        $this->scriptManager = new ScriptManager($this->options); //TODO: options?
    }

    /**
     * Magic function to reduce repetitive coding and create aliases.
     *
     * @since  1.0.0
     * @param  string $method Name of method
     * @param  array  $args   Passed arguments
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabelException
     * @throws \Khill\Lavacharts\Exceptions\InvalidFormatType
     * @return mixed Returns Charts, Dashboards, DataTables, Formats and Filters
     */
    public function __call($method, $args)
    {
        if (in_array($method, self::DATATABLE_TYPES)) {
            return call_user_func_array([DataFactory::class, $method], $args);
        }

        if (in_array($method, self::VOLCANO_METHODS)) {
            return call_user_func_array([$this->volcano, $method], $args);
        }

        if (Str::endsWith($method, 'Chart')) {
            return $this->createChart($method, $args);
        }

        if (Str::endsWith($method, 'Filter')) {
            return FilterFactory::create($method, $args);
        }

        if (Str::endsWith($method, 'Format')) {
            if (! in_array($method, Format::TYPES)) {
                throw new InvalidFormatType($method);
            }

            return new Format($method, $args[0]);
        }

        throw new \BadMethodCallException(
            sprintf('Unknown method "%s" in "%s".', $method, get_class())
        );
    }

    /**
     * Convert the Lavacharts object to an array
     *
     * @since 4.0.0
     * @return array
     */
    public function toArray()
    {
        return [
            'options'     => $this->options,
            'renderables' => $this->volcano,
        ];
    }

    /**
     * Run the library and get the resulting scripts.
     *
     *
     * This method will create a <script> for the lava.js module along with
     * one additional <script> with all of the charts & dashboards.
     *
     * @since 4.0.0
     * @return string HTML script elements
     */
    public function flow()
    {
        return $this->renderAll();

//        @TODO This is the goal :)
//        return new ScriptManager($this->options, json_encode($this));
    }

    /**
     * Create a new Dashboard
     *
     * @since 4.0.0 Changing method signature
     * @since 3.0.0
     * @param string             $label
     * @param DataInterface|null $datatable
     * @param array              $options
     * @return Dashboard
     * @throws InvalidLabelException
     */
    public function Dashboard($label, DataInterface $datatable = null, array $options = [])
    {
        $dashboard = new Dashboard($label, $datatable, $options);

        $this->volcano->store($dashboard);

        return $dashboard;
    }

    /**
     * Create a new ControlWrapper from a Filter
     *
     * @since  3.0.0
     * @param  Filter $filter    Filter to wrap
     * @param  string $elementId HTML element ID to output the control.
     * @return ControlWrapper
     */
    public function ControlWrapper(Filter $filter, $elementId)
    {
        $elementId = Str::verify($elementId);

        return new ControlWrapper($filter, $elementId);
    }

    /**
     * Create a new ChartWrapper from a Chart
     *
     * @since  3.0.0
     * @param  Chart  $chart     Chart to wrap
     * @param  string $elementId HTML element ID to output the control.
     * @return ChartWrapper
     */
    public function ChartWrapper(Chart $chart, $elementId)
    {
        $elementId = Str::verify($elementId);

        return new ChartWrapper($chart, $elementId);
    }

    /**
     * Returns the Volcano instance.
     *
     * @return Volcano
     */
    public function getVolcano()
    {
        return $this->volcano;
    }

    /**
     * Returns the current locale used in the DataTable
     *
     * @deprecated 4.0.0 use $lava->getOption('locale')
     * @since      3.1.0
     * @return string
     */
    public function getLocale()
    {
        return $this->options['locale'];
    }

    /**
     * Locales are used to customize text for a country or language.
     *
     * This will affect the formatting of values such as currencies, dates, and numbers.
     *
     * By default, Lavacharts is loaded with the "en" locale. You can override this default
     * by explicitly specifying a locale when creating the DataTable.
     *
     * @deprecated 4.0.0 Set this option with the constructor, or with
     *                   $lava->options->set('locale', 'en');
     * @since      3.1.0
     * @param  string $locale
     * @return $this
     */
    public function setLocale($locale = 'en')
    {
        $this->options['locale'] = Str::verify($locale);

        return $this;
    }

    /**
     * Outputs the lava.js module for manual placement.
     *
     * @since 3.0.3
     * @param array $options
     * @return string Google Chart API and lava.js script blocks
     */
    public function lavajs(array $options = [])
    {
        $this->options->merge($options);

        return (string) $this->scriptManager->getLavaJs($this->options->toArray());
    }

    /**
     * The render method is depreciated.
     *
     * @deprecated 4.0
     * @throws \Khill\Lavacharts\Exceptions\DepreciatedMethodException
     */
    public function render()
    {
        $msg  = 'As of Lavacharts 4.0, the render() method has been depreciated. ';
        $msg .= 'Please refer to the migration guide for instructions on upgrading to the new syntax.';

        trigger_error($msg, E_DEPRECATED);

//        TODO: call renderAll() and bypass subsequent render() calls
//        $this->renderAll();
    }

    /**
     * Renders all charts and dashboards that have been defined.
     *
     * Options can be passed in to override the default config.
     * Available options are defined in src/Laravel/config/lavacharts.php
     *
     * @since 3.1.0
     * @since 4.0.0 Takes options and merges them with existing options.
     * @param array $options Options for rendering
     * @return string
     */
    public function renderAll(array $options = [])
    {        // TODO: this fails silently if the chart doesn't have an elementId
        $this->options->merge($options);

        if (! $this->scriptManager->lavaJsLoaded()) {
            $this->scriptManager->loadLavaJs($this->options);
        }

        if (count($this->volcano) > 0) {
            $this->scriptManager->openScriptTag();

            /** @var Renderable $renderable */
            foreach ($this->volcano as $renderable) {
                if ($renderable->isRenderable()) {
                    $this->scriptManager->addRenderableToOutput($renderable);
                }
            }

            $this->scriptManager->closeScriptTag();
        }

        return $this->scriptManager->getOutputBuffer();
    }

    /**
     * Create a new Chart of the given type.
     *
     * @since 4.0.0
     * @param string $type
     * @param array  $args
     * @return Chart
     * @throws InvalidLabelException
     */
    protected function createChart($type, $args)
    {
        $chart = ChartFactory::create($type, $args);

        $this->volcano->store($chart);

        return $chart;
    }

    /**
     * Checks if running in composer environment
     *
     * This will check if the folder 'composer' is within the path to Lavacharts.
     *
     * @access private
     * @since  2.4.0
     * @return boolean
     */
    private function usingComposer()
    {
        if (strpos(realpath(__FILE__), 'composer') !== false) {
            return true;
        } else {
            return false;
        }
    }
}
