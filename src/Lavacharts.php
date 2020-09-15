<?php

declare(strict_types=1);

namespace Khill\Lavacharts;

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\Dashboards\Dashboard;
use Khill\Lavacharts\Dashboards\Filter;
use Khill\Lavacharts\Exceptions\InvalidDataTable;
use Khill\Lavacharts\Exceptions\InvalidChartType;
use Khill\Lavacharts\Exceptions\RenderableNotFound;
use Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper;
use Khill\Lavacharts\Exceptions\BadMethodCallException;
use Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper;
use Khill\Lavacharts\DataTables\DataFactory;
use Khill\Lavacharts\DataTables\Columns\Format;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Javascript\ScriptManager;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Contracts\DataInterface;
use Khill\Lavacharts\Support\Contracts\Jsonable;
use Khill\Lavacharts\Support\Options;
use Khill\Lavacharts\Support\Renderable;
use Khill\Lavacharts\Support\StringValue as Str;
use Khill\Lavacharts\Exceptions\InvalidArgumentException;
use Khill\Lavacharts\Exceptions\InvalidElementIdException;
use Khill\Lavacharts\Support\Traits\ArrayToJsonTrait as ArrayToJson;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;

/**
 * Lavacharts - A PHP wrapper library for the Google Chart API
 *
 * @category  Class
 * @package   Khill\Lavacharts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @license   http://opensource.org/licenses/MIT      MIT
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 *
 * @method DataTable(array $options = [])
 * @method JoinedDataTable(DataInterface $data1, DataInterface $data2, array $options = [])
 * @method ArrowFormat($labelOrIndex, array $option = [])
 * @method BarFormat($labelOrIndex, array $option = [])
 * @method DateFormat($labelOrIndex, array $option = [])
 * @method NumberFormat($labelOrIndex, array $option = [])
 * @method CategoryFilter($labelOrIndex, array $option = [])
 * @method ChartRangeFilter($labelOrIndex, array $option = [])
 * @method DateRangeFilter($labelOrIndex, array $option = [])
 * @method NumberRangeFilter($labelOrIndex, array $option = [])
 * @method StringFilter($labelOrIndex, array $option = [])
 */
class Lavacharts implements Customizable, Jsonable, Arrayable
{
    use HasOptions, ArrayToJson;

    /**
     * Lavacharts version
     */
    const VERSION = '4.0.0';

    /**
     * DataTable types to map with __call()
     */
    const DATATABLE_TYPES = [
        'DataTable',
        'JoinedDataTable',
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
     * Get the default set of options used by Lavacharts.
     *
     * @since  4.0.0
     * @return array
     */
    public static function getDefaultOptions()
    {
        return Options::getDefault();
    }

    /**
     * Lavacharts constructor.
     *
     * @param array $options Customization options
     * @throws InvalidArgumentException
     */
    public function __construct(array $options = [])
    {
        $this->initOptions($options);

        $this->volcano       = new Volcano();
        $this->scriptManager = new ScriptManager();

        $this->scriptManager->setOptions($this->options);
        $this->scriptManager->setVolcano($this->volcano);
    }

    /**
     * Magic function to create aliases methods for common classes.
     *
     * @param string $method Name of method
     * @param array  $args   Passed arguments
     *
     * @since  1.0.0
     * @return Chart|Dashboard|DataTable|Format|Filter
     * @throws BadMethodCallException
     * @throws InvalidChartType
     * @throws InvalidDataTable
     */
    public function __call($method, $args)
    {
        if (in_array($method, self::DATATABLE_TYPES)) {
            return call_user_func_array([DataFactory::class, $method], $args);
        }

        if (in_array($method, ChartFactory::TYPES)) {
            return $this->createChart($method, $args);
        }

        if (in_array($method, Format::TYPES)) {
            return Format::create($method, $args);
        }

        if (in_array($method, Filter::TYPES)) {
            return Filter::create($method, $args);
        }

        throw new BadMethodCallException($this, $method);
    }

    /**
     * Run the library and get the resulting scripts.
     *
     * This method will create a <script> tag for the lava.js module, if
     * it is not bypassed or already output, along with one additional
     * <script> block with all of the charts & dashboards.
     *
     * Options can be passed in to override the default config.
     * Available options are defined in src/Laravel/config/lavacharts.php
     *
     * @param array $options Array of options to override defaults before script output.
     *
     * @return string HTML script elements
     * @throws InvalidElementIdException
     * @since  4.0.0
     */
    public function run(array $options = [])
    {
        $this->scriptManager->mergeOptions($options);

        return $this->scriptManager->getScriptTags();
    }

    /**
     * Convert the Lavacharts object to an array
     *
     * @since  4.0.0
     * @return array
     */
    public function toArray()
    {
        return [
            'options'     => $this->options->toArray(),
            'renderables' => $this->volcano->toArray(),
        ];
    }

    /**
     * Shortcut method for accessing store() on the Volcano
     *
     * @param Renderable $renderable
     *
     * @since  3.0.0 Simplified to forward the call to the Volcano.
     * @return Renderable
     */
    public function store(Renderable $renderable)
    {
        return $this->volcano->store($renderable);
    }

    /**
     * Shortcut method for accessing exists() on the Volcano
     *
     * @param string $label
     *
     * @since  4.0.0 Simplified to forward the call to the Volcano.
     * @since  2.4.2 Initial Implementation
     * @return bool
     */
    public function exists(string $label): bool
    {
        return $this->volcano->exists($label);
    }

    /**
     * Shortcut method for accessing get() on the Volcano
     *
     * @param string $label Label of the {@link Chart} to retrieve
     *
     * @since  4.0.0 Renamed fetch, forwards the call to the Volcano.
     * @since  3.0.0 Initial Implementation
     * @return Renderable
     * @throws RenderableNotFound
     */
    public function get(string $label)
    {
        return $this->volcano->get($label);
    }

    /**
     * Get an instance of the DataFactory
     *
     * @since  4.0.0
     * @return DataFactory
     */
    public function DataFactory(): DataFactory
    {
        return new DataFactory;
    }

    /**
     * Create a new Format based by named type.
     *
     * @since 4.0.0
     * @param string $type
     * @param array  $args
     * @return Format
     */
    public function Format($type, ...$args)
    {
        return Format::create($type, $args);
    }

    /**
     * Create a new Filter based by named type.
     *
     * @since 4.0.0
     * @param string $type
     * @param array  $args
     * @return Filter
     */
    public function Filter($type, ...$args): Filter
    {
        return Filter::create($type, $args);
    }

    /**
     * Create a new Dashboard
     *
     * @since 4.0.0 Changing method signature
     * @since 3.0.0
     * @param string             $label
     * @param DataInterface|null $data
     * @param array              $options
     * @return Dashboard
     */
    public function Dashboard($label, DataInterface $data = null, array $options = [])
    {
        $dashboard = new Dashboard($label, $data, $options);

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
    public function ControlWrapper(Filter $filter, string $elementId)
    {
        $elementId = Str::verify($elementId);

        return new ControlWrapper($filter, $elementId);
    }

    /**
     * Create a new ChartWrapper from a Chart
     *
     * @since  4.0.0 Allowing string named types of charts along with Chart objects
     * @since  3.0.0
     * @param  Chart|string $chartType Chart to wrap or type of chart to create and wrap.
     * @param  string       $elementId HTML element ID to output the control.
     * @return ChartWrapper
     */
    public function ChartWrapper($chartType, $elementId) //TODO: add options to the signature
    {
        $elementId = Str::verify($elementId);

        return new ChartWrapper($chartType, $elementId);
    }

    /**
     * Returns the Volcano instance.
     *
     * @since 4.0.0
     * @return Volcano
     */
    public function getVolcano(): Volcano
    {
        return $this->volcano;
    }

    /**
     * Returns the ScriptManager instance.
     *
     * @since 3.1.9
     * @return ScriptManager
     */
    public function getScriptManager()
    {
        return $this->scriptManager;
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
     *                   $lava->setOption('locale', 'en');
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
     * Outputs the lava.js module <script> block for manual placement.
     *
     * @since 3.0.3
     * @param array $options
     * @return string Google Chart API and lava.js script blocks
     */
    public function lavaJs(array $options = [])
    {
        $options = new Options($options);

        $this->scriptManager->mergeOptions($options);

        return (string) $this->scriptManager->getLavaJs();
    }

    /**
     * The render method is depreciated.
     *
     * @deprecated 4.0.0
     * @throws InvalidElementIdException
     */
    public function render()
    {
        $msg  = 'As of Lavacharts 4.0, the render() method has been depreciated. ';
        $msg .= 'Please refer to the migration guide for instructions on upgrading to the new syntax.';

        trigger_error($msg, E_USER_WARNING);

//        TODO: bypass any subsequent render() calls
        $this->renderAll();
        // TODO: call run() instead.
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
     * @throws InvalidElementIdException
     */
    public function renderAll(array $options = [])
    {
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
     * @throws InvalidChartType
     * @throws InvalidDataTable
     */
    private function createChart(string $type, array $args)
    {
        $chart = ChartFactory::create($type, $args);

        $this->volcano->store($chart);

        return $chart;
    }
}
