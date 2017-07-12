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
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\DataTables\Formats\FormatFactory;
use Khill\Lavacharts\Exceptions\InvalidArgumentException;
use Khill\Lavacharts\Exceptions\InvalidLabel;
use Khill\Lavacharts\Exceptions\InvalidLabelException;
use Khill\Lavacharts\Exceptions\RenderableNotFound;
use Khill\Lavacharts\Javascript\ScriptManager;
use Khill\Lavacharts\Support\Args;
use Khill\Lavacharts\Support\Contracts\Arrayable;
use Khill\Lavacharts\Support\Contracts\Customizable;
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
 * @method exists
 * @method store
 * @method get
 */
class Lavacharts implements Customizable, Jsonable, Arrayable
{
    use HasOptions, ArrayToJson;

    /**
     * Lavacharts version
     */
    const VERSION = '3.2.0';

    const VOLCANO_METHODS = [
        'store',
        'exists',
        'get',
        'getCharts',
        'getDashboards',
    ];

    const DATATABLE_TYPES = [
        'DataTable',
        'JoinedDataTable',
    ];

    const BASE_LAVA_CLASSES = [
        'ChartWrapper',
        'ControlWrapper',
        'DataTable',
        'DataFactory',
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
        $this->scriptManager = new ScriptManager($this->options);
    }

    /**
     * Magic function to reduce repetitive coding and create aliases.
     *
     * @since  1.0.0
     *
     * @param  string $method Name of method
     * @param  array  $args   Passed arguments
     *
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabelException
     * @throws \Khill\Lavacharts\Exceptions\InvalidRenderable
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
            return self::Chart($method, $args);
        }

        if (Str::endsWith($method, 'Filter')) {
            return FilterFactory::create($method, $args);
        }

        if (Str::endsWith($method, 'Format')) {
            return FormatFactory::create($method, $args);
        }

        throw new \BadMethodCallException(
            sprintf('Unknown method "%s" in "%s".', $method, get_class())
        );
    }

    /**
     * Run the library and get the resulting scripts.
     *
     *
     * This method will create a <script> for the lava.js module along with
     * one additional <script> per chart & dashboard being rendered.
     *
     * @since 3.2.0
     * @return string HTML script elements
     */
    public function flow()
    {
        return $this->renderAll();

//        @TODO This is the goal :)
//        return new ScriptManager($this->options, json_encode($this));
    }

    /**
     * Convert the Lavacharts object to an array
     *
     * @since 3.2.0
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
     * Create a new Chart of the given type.
     *
     * @since 3.2.0
     * @param string $type
     * @param array  $args
     * @return Chart
     * @throws InvalidLabelException
     */
    public function Chart($type, $args)
    {
        if (! isset($args[0])) {
            throw new InvalidLabelException;
        }

        $label = Str::verify($args[0]);

        if ($this->volcano->exists($label)) {
            return $this->volcano->get($label);
        }

        $chart = ChartFactory::create($type, $args);

        $this->volcano->store($chart);

        return $chart;
    }

    /**
     * Create a new Dashboard
     *
     * @since 3.0.0
     * @param string $label
     * @param array  $args
     * @return Dashboard
     * @throws InvalidLabelException
     */
    public function Dashboard($label, ...$args)
    {
        try {
            $label = Str::verify($label);
        } catch (InvalidArgumentException $e) {
            throw new InvalidLabelException;
        }

        if ($this->volcano->exists($label)) {
            return $this->volcano->get($label);
        }

        $dashboard = DashboardFactory::create($label, $args);

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
     * @deprecated 3.2.0 use $lava->getOption('locale')
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
     * @deprecated 3.2.0 Set this option with the constructor, or with
     *                   $lava->options->set('locale', 'en');
     *
     * @since      3.1.0
     *
     * @param  string $locale
     *
     * @return $this
     * @throws \Khill\Lavacharts\Exceptions\InvalidStringValue
     */
    public function setLocale($locale = 'en')
    {
        $this->options['locale'] = Str::verify($locale);

        return $this;
    }

    /**
     * Outputs the lava.js module for manual placement.
     *
     * Will be depreciating jsapi in the future
     *
     * @since  3.0.3
     *
     * @param array $options
     *
     * @return string Google Chart API and lava.js script blocks
     */
    public function lavajs(array $options = [])
    {
        $this->options->merge($options);

        return (string) $this->scriptManager->getLavaJs($this->options);
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
     * @since  3.2.0  Type and div creation were removed.
     *
     * @param  string $label     Label of the object to render.
     * @param  string $elementId HTML element id to render into.
     *
     * @return string
     */
    public function render($label, $elementId = '')
    {
        $label     = Str::verify($label);
        $elementId = Str::verify($elementId);

        $renderable = $this->volcano->get($label);

        if (! $renderable->hasOption('elementId')) {
            $renderable->setElementId($elementId);
        }

        $buffer = $this->scriptManager->getOutputBuffer($renderable);

        if ($this->scriptManager->lavaJsLoaded() === false) {
            $buffer->prepend($this->lavajs());
        }

        return $buffer;
    }

    /**
     * Renders all charts and dashboards that have been defined.
     *
     *
     * Options can be passed in to override the default config.
     * Available options are defined in src/Laravel/config/lavacharts.php
     *
     * @since 3.1.0
     * @since 3.2.0 Takes options and merges them with existing options.
     *
     * @param array $options Options for rendering
     *
     * @return string
     */
    public function renderAll(array $options = [])
    {
        $this->scriptManager->mergeOptions($options);

        $output = $this->scriptManager->getLavaJs($this->options);

        /** @var Renderable $renderable */
        foreach ($this->volcano as $renderable) {
            if ($renderable->isRenderable()) {
                $output->append(
                    $this->scriptManager->getOutputBuffer($renderable)
                );
            }
        }

        return $output->getContents();
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
