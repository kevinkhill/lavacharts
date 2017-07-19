<?php

namespace Khill\Lavacharts\Dashboards\Bindings;

use Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper;
use Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper;
use Khill\Lavacharts\Dashboards\Wrappers\Wrapper;
use Khill\Lavacharts\Javascript\JavascriptSource;
use Khill\Lavacharts\Support\Contracts\Jsonable;
use Khill\Lavacharts\Support\Traits\ToJavascriptTrait as ToJavascript;

/**
 * Parent Binding Class
 *
 * Binds a ControlWrapper to a ChartWrapper to use in dashboards.
 *
 * @package   Khill\Lavacharts\Dashboards\Bindings
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class Binding extends JavascriptSource implements Jsonable
{
    use ToJavascript;

    /**
     * Array of ControlWrappers.
     *
     * @var ControlWrapper[]
     */
    protected $controlWrappers;

    /**
     * Array of ChartWrappers.
     *
     * @var ChartWrapper[]
     */
    protected $chartWrappers;

    /**
     * Returns the type of binding.
     *
     * @return string
     */
    public function getType()
    {
        $parts = explode('\\', static::class);

        return array_pop($parts);
    }

    /**
     * Assigns the wrappers and creates the new Binding.
     *
     * @param ChartWrapper[]   $chartWrappers
     * @param ControlWrapper[] $controlWrappers
     */
    public function __construct(array $controlWrappers, array $chartWrappers)
    {
        $this->chartWrappers   = $chartWrappers;
        $this->controlWrappers = $controlWrappers;
    }

    /**
     * Get the ChartWrappers
     *
     * @return ChartWrapper[]
     */
    public function getChartWrappers()
    {
        return $this->chartWrappers;
    }

    /**
     * Get the a specific ChartWrap
     *
     * @since  3.1.0
     * @param  int $index Which chart wrap to retrieve
     * @return ChartWrapper
     */
    public function getChartWrap($index)
    {
        return $this->chartWrappers[$index];
    }

    /**
     * Get the ControlWrappers
     *
     * @return ControlWrapper[]
     */
    public function getControlWrappers()
    {
        return $this->controlWrappers;
    }

    /**
     * Get the a specific ControlWrap
     *
     * @since  3.1.0
     * @param  int $index Which control wrap to retrieve
     * @return ControlWrapper
     */
    public function getControlWrap($index)
    {
        return $this->controlWrappers[$index];
    }

    /**
     * Return a format string that will be used by vsprintf to convert the
     * extending class to javascript.
     *
     * @return string
     */
    public function getJavascriptFormat()
    {
        // "this" refers to the lava.Dashboard object
        return 'this.dashboard.bind(%s, %s);';
    }

    /**
     * Return an array of arguments to pass to the format string provided
     * by getJavascriptFormat().
     *
     * These variables will be used with vsprintf, and the format string
     * to convert the extending class to javascript.
     *
     * @return array
     */
    public function getJavascriptSource()
    {
        return [
            $this->wrappersToJavascript($this->controlWrappers),
            $this->wrappersToJavascript($this->chartWrappers)
        ];
    }

    /**
     * Map the wrapper values from the array to javascript notation.
     *
     * @access private
     * @param  array $wrapperArray Array of control or chart wrappers
     * @return string Json notation for the wrappers
     */
    private function wrappersToJavascript($wrapperArray)
    {
        if (count($wrapperArray) == 1) {
            return $wrapperArray[0]->toJavascript();
        }

        $wrappers = array_map(function (Wrapper $wrapper) {
            return $wrapper->toJavascript();
        }, $wrapperArray);

        return '[' . implode(',', $wrappers) . ']';
    }

    /**
     * Returns a customize JSON representation of an object.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * Custom serialization of the chart.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            $this->controlWrappers,
            $this->chartWrappers
        ];
    }
}
