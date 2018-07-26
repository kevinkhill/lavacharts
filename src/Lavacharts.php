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
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\DataTables\Formats\Format;
use Khill\Lavacharts\Exceptions\InvalidElementId;
use Khill\Lavacharts\Exceptions\InvalidLabel;
use Khill\Lavacharts\Exceptions\InvalidLavaObject;
use Khill\Lavacharts\Javascript\ScriptManager;
use Khill\Lavacharts\Support\Config;
use Khill\Lavacharts\Support\Html\HtmlFactory;
use Khill\Lavacharts\Support\Psr4Autoloader;
use Khill\Lavacharts\Values\ElementId;
use Khill\Lavacharts\Values\Label;
use Khill\Lavacharts\Values\StringValue;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;
use Khill\Lavacharts\Support\Contracts\RenderableInterface as Renderable;

require(__DIR__.'/Support/Traits/HasOptionsTrait.php');

/**
 * Lavacharts - A PHP wrapper library for the Google Chart API
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Lavacharts
{
    use HasOptions;

    /**
     * Lavacharts version
     */
    const VERSION = '3.1.11';

    /**
     * Locale for the Charts and Dashboards.
     *
     * @var string
     */
    private $locale = 'en';

    /**
     * Holds all of the defined Charts and DataTables.
     *
     * @var \Khill\Lavacharts\Volcano
     */
    private $volcano;

    /**
     * ScriptManager for outputting lava.js and chart/dashboard javascript
     *
     * @var \Khill\Lavacharts\Javascript\ScriptManager
     */
    private $scriptManager;

    /**
     * Lavacharts constructor.
     */
    public function __construct(array $options = [])
    {
        if ( ! $this->usingComposer()) {
            require_once(__DIR__.'/Support/Psr4Autoloader.php');

            $loader = new Psr4Autoloader;
            $loader->register();
            $loader->addNamespace('Khill\Lavacharts', __DIR__);
        }

        $this->initializeOptions($options);

        $this->volcano       = new Volcano;
        $this->chartFactory  = new ChartFactory;
        $this->dashFactory   = new DashboardFactory;
        $this->scriptManager = new ScriptManager($this->options);
    }

    /**
     * Magic function to reduce repetitive coding and create aliases.
     *
     * @since  1.0.0
     * @param  string $method Name of method
     * @param  array  $args   Passed arguments
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidLavaObject
     * @throws \Khill\Lavacharts\Exceptions\InvalidFunctionParam
     * @return mixed Returns Charts, Formats and Filters
     */
    public function __call($method, $args)
    {
        //Charts
        if (ChartFactory::isValidChart($method)) {
            if (isset($args[0]) === false) {
                throw new InvalidLabel;
            }

            if ($this->exists($method, $args[0])) {
                $label = new Label($args[0]);

                $lavaClass = $this->volcano->get($method, $label);
            } else {
                $chart = $this->chartFactory->create($method, $args);

                $lavaClass = $this->volcano->store($chart);
            }
        }

        //Filters
        if ((bool) preg_match('/Filter$/', $method)) {
            $options = isset($args[1]) ? $args[1] : [];

            $lavaClass = FilterFactory::create($method, $args[0], $options);
        }

        //Formats
        if ((bool) preg_match('/Format$/', $method)) {
            $options = isset($args[0]) ? $args[0] : [];

            $lavaClass = Format::create($method, $options);
        }

        if (isset($lavaClass) == false) {
            throw new InvalidLavaObject($method);
        }

        return $lavaClass;
    }

    /**
     * Get the ScriptManager instance.
     *
     * @since 3.1.9
     * @return ScriptManager
     */
    public function getScriptManager()
    {
        return $this->scriptManager;
    }

    /**
     * Create a new DataTable using the DataFactory
     *
     * If the additional DataTablePlus package is available, then one will
     * be created, otherwise a standard DataTable is returned.
     *
     * @since  3.0.3
     * @uses   \Khill\Lavacharts\DataTables\DataFactory
     * @param  mixed $args
     * @return \Khill\Lavacharts\DataTables\DataTable
     */
    public function DataTable($args = null)
    {
        $dataFactory = __NAMESPACE__.'\\DataTables\\DataFactory::DataTable';

        return call_user_func_array($dataFactory, func_get_args());
    }

    /**
     * Create a new Dashboard
     *
     * @since  3.0.0
     * @param  string                                 $label
     * @param  \Khill\Lavacharts\DataTables\DataTable $dataTable
     * @return \Khill\Lavacharts\Dashboards\Dashboard
     */
    public function Dashboard($label, DataTable $dataTable)
    {
        if ($this->exists('Dashboard', $label)) {
            $dashboard = $this->volcano->get('Dashboard', $label);
        } else {
            $dashboard = $this->volcano->store(
                $this->dashFactory->create(func_get_args())
            );
        }

        return $dashboard;
    }

    /**
     * Create a new ControlWrapper from a Filter
     *
     * @since  3.0.0
     * @uses   \Khill\Lavacharts\Values\ElementId
     * @param  \Khill\Lavacharts\Dashboards\Filters\Filter $filter Filter to wrap
     * @param  string $elementId HTML element ID to output the control.
     * @return \Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper
     */
    public function ControlWrapper(Filter $filter, $elementId)
    {
        $elementId = new ElementId($elementId);

        return new ControlWrapper($filter, $elementId);
    }

    /**
     * Create a new ChartWrapper from a Chart
     *
     * @since  3.0.0
     * @uses   \Khill\Lavacharts\Values\ElementId
     * @param  \Khill\Lavacharts\Charts\Chart $chart Chart to wrap
     * @param  string $elementId HTML element ID to output the control.
     * @return \Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper
     */
    public function ChartWrapper(Chart $chart, $elementId)
    {
        $elementId = new ElementId($elementId);

        return new ChartWrapper($chart, $elementId);
    }

    /**
     * Locales are used to customize text for a country or language.
     *
     * This will affect the formatting of values such as currencies, dates, and numbers.
     *
     * By default, Lavacharts is loaded with the "en" locale. You can override this default
     * by explicitly specifying a locale when creating the DataTable.
     *
     * @since  3.1.0
     * @param  string $locale
     * @return $this
     * @throws \Khill\Lavacharts\Exceptions\InvalidStringValue
     */
    public function setLocale($locale = 'en')
    {
        $this->locale = new StringValue($locale);

        return $this;
    }
    /**
     * Returns the current locale used in the DataTable
     *
     * @since  3.1.0
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Outputs the lava.js module for manual placement.
     *
     * Will be depreciating jsapi in the future
     *
     * @since  3.0.3
     * @return string Google Chart API and lava.js script blocks
     */
    public function lavajs()
    {
        $config = [
            'locale' => $this->locale
        ];

        return (string) $this->scriptManager->getLavaJsModule($config);
    }

    /**
     * Outputs the link to the Google JSAPI
     *
     * @since      2.3.0
     * @deprecated 3.0.3
     * @return string Google Chart API and lava.js script blocks
     */
    public function jsapi()
    {
        return $this->lavajs();
    }

    /**
     * Checks to see if the given chart or dashboard exists in the volcano storage.
     *
     * @since  2.4.2
     * @uses   \Khill\Lavacharts\Values\Label
     * @param  string $type Type of object to isNonEmpty.
     * @param  string $label Label of the object to isNonEmpty.
     * @return boolean
     */
    public function exists($type, $label)
    {
        $label = new Label($label);

        if ($type == 'Dashboard') {
            return $this->volcano->checkDashboard($label);
        } else {
            return $this->volcano->checkChart($type, $label);
        }
    }

    /**
     * Fetches an existing Chart or Dashboard from the volcano storage.
     *
     * @since  3.0.0
     * @uses   \Khill\Lavacharts\Values\Label
     * @param  string $type  Type of Chart or Dashboard.
     * @param  string $label Label of the Chart or Dashboard.
     * @return \Khill\Lavacharts\Support\Contracts\RenderableInterface
     * @throws \Khill\Lavacharts\Exceptions\InvalidLavaObject
     */
    public function fetch($type, $label)
    {
        $label = new Label($label);

        if (strpos($type, 'Chart') === false && $type != 'Dashboard') {
            throw new InvalidLavaObject($type);
        }

        return $this->volcano->get($type, $label);
    }

    /**
     * Stores a existing Chart or Dashboard into the volcano storage.
     *
     * @since  3.0.0
     * @param  \Khill\Lavacharts\Support\Contracts\RenderableInterface $renderable A Chart or Dashboard.
     * @return \Khill\Lavacharts\Support\Contracts\RenderableInterface
     */
    public function store(Renderable $renderable)
    {
        return $this->volcano->store($renderable);
    }

    /**
     * Renders Charts or Dashboards into the page
     *
     * Given a type, label, and HTML element id, this will output
     * all of the necessary javascript to generate the chart or dashboard.
     *
     * As of version 3.1, the elementId parameter is optional, but only
     * if the elementId was set explicitly to the Renderable.
     *
     * @since  2.0.0
     * @uses   \Khill\Lavacharts\Values\Label
     * @uses   \Khill\Lavacharts\Values\ElementId
     * @uses   \Khill\Lavacharts\Support\Buffer
     * @param  string $type       Type of object to render.
     * @param  string $label      Label of the object to render.
     * @param  mixed  $elementId  HTML element id to render into.
     * @param  mixed  $div        Set true for div creation, or pass an array with height & width
     * @return string
     */
    public function render($type, $label, $elementId = null, $div = false)
    {
        $label = new Label($label);

        try {
            $elementId = new ElementId($elementId);
        } catch (InvalidElementId $e) {
            $elementId = null;
        }

        if (is_array($elementId)) {
            $div = $elementId;
        }

        if ($type == 'Dashboard') {
            $buffer = $this->renderDashboard($label, $elementId);
        } else {
            $buffer = $this->renderChart($type, $label, $elementId, $div);
        }

        return $buffer->getContents();
    }

    /**
     * Renders all charts and dashboards that have been defined
     *
     * @since  3.1.0
     * @return string
     */
    public function renderAll()
    {
        $output = '';

        if ($this->scriptManager->lavaJsRendered() === false) {
            $output = $this->scriptManager->getLavaJsModule();
        }

        $renderables = $this->volcano->getAll();

        foreach ($renderables as $renderable) {
            $output .= $this->scriptManager->getOutputBuffer($renderable);
        }

        return $output;
    }

    /**
     * Renders the chart into the page
     *
     * Given a chart label and an HTML element id, this will output
     * all of the necessary javascript to generate the chart.
     *
     * @since  3.0.0
     * @param  string                             $type
     * @param  \Khill\Lavacharts\Values\Label     $label
     * @param  \Khill\Lavacharts\Values\ElementId $elementId HTML element id to render the chart into.
     * @param  bool|array                         $div       Set true for div creation, or pass an array with height & width
     * @return \Khill\Lavacharts\Support\Buffer
     * @throws \Khill\Lavacharts\Exceptions\ChartNotFound
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidDivDimensions
     */
    private function renderChart($type, Label $label, ElementId $elementId = null, $div = false)
    {
        /** @var \Khill\Lavacharts\Charts\Chart $chart */
        $chart = $this->volcano->get($type, $label);

        if ($elementId === null) {
            $elementId = $chart->getElementId();
        }

        if ($elementId instanceof ElementId) {
            $chart->setElementId($elementId);
        }

        $buffer = $this->scriptManager->getOutputBuffer($chart);

        if ($this->scriptManager->lavaJsRendered() === false) {
            $buffer->prepend($this->lavajs());
        }

        if ($div !== false) {
            $buffer->prepend(HtmlFactory::createDiv($chart->getElementIdStr(), $div));
        }

        return $buffer;
    }

    /**
     * Renders the chart into the page
     * Given a chart label and an HTML element id, this will output
     * all of the necessary javascript to generate the chart.
     *
     * @since  3.0.0
     * @uses   \Khill\Lavacharts\Support\Buffer   $buffer
     * @param  \Khill\Lavacharts\Values\Label     $label
     * @param  \Khill\Lavacharts\Values\ElementId $elementId HTML element id to render the chart into.
     * @return \Khill\Lavacharts\Support\Buffer
     * @throws \Khill\Lavacharts\Exceptions\DashboardNotFound
     */
    private function renderDashboard(Label $label, ElementId $elementId = null)
    {
        /** @var \Khill\Lavacharts\Dashboards\Dashboard $dashboard */
        $dashboard = $this->volcano->get('Dashboard', $label);

        if ($elementId instanceof ElementId) {
            $dashboard->setElementId($elementId);
        }

        $buffer = $this->scriptManager->getOutputBuffer($dashboard);

        if ($this->scriptManager->lavaJsRendered() === false) {
            $buffer->prepend($this->lavajs());
        }

        return $buffer;
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

    /**
     * Initialize the default options from file while overriding with user
     * passed values.
     *
     * @param array $options
     * @return void
     */
    private function initializeOptions(array $options)
    {
        $this->setOptions(Config::getDefault());

        $this->options->merge($options);
    }
}
