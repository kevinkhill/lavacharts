<?php

namespace Khill\Lavacharts\Dashboard;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Exceptions\InvalidLabel;
use \Khill\Lavacharts\Exceptions\InvalidFunctionParam;

class Dashboard implements \JsonSerializable
{
    /**
     * Google's dashboard version
     *
     * @var string
     */
    const VERSION = '1';

    /**
     * Javascript chart package.
     *
     * @var string
     */
    const VIZ_PACKAGE = 'controls';

    /**
     * Javascript chart class.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.Dashboard';

    /**
     * The dashboard's unique label.
     *
     * @var \Khill\Lavacharts\Values\Label
     */
    private $label = null;

    /**
     * Arry of Binding objects, mapping controls to charts.
     *
     * @var array
     */
    private $bindings = [];

    /**
     * Builds a new Dashboard with identifying label.
     *
     * @param  string $label
     * @return self
     */
    public function __construct(Label $label)
    {
        $this->label = $label;
    }

    /**
     * Binds a ControlWrapper to a ChartWrapper in the dashboard.
     *
     * @param  string $label Label for the binding
     * @param  \Khill\Lavacharts\Dashboard\ChartWrapper   $chartWrap
     * @param  \Khill\Lavacharts\Dashboard\ControlWrapper $controlWrap
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel  $label
     * @return self
     */
    public function bind($label, ControlWrapper $controlWrap, ChartWrapper $chartWrap)
    {
        if (Utils::nonEmptyString($label) === false) {
            throw new InvalidLabel($label);
        }

        $this->bindings[$label] = new Binding($label, $controlWrap, $chartWrap);

        return $this;
    }

    /**
     * Fetch a binding by label.
     *
     * @param  string $label Label for the binding
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel  $label
     * @return \Khill\Lavacharts\Dashboard\Binding
     */
    public function getBinding($label)
    {
        if (Utils::nonEmptyString($label) === false) {
            throw new InvalidLabel($label);
        }

        return $this->bindings[$label];
    }

    /**
     * Fetch the dashboard's bindings.
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Returns the chart label.
     *
     * @since  3.0.0
     * @return \Khill\Lavacharts\Values\Label
     */
    public function getLabel()
    {
        return (string) $this->label;
    }

    /**
     * Custom JSON serialization of the Dashboard.
     *
     * @return string JSON
     */
    public function jsonSerialize()
    {
        return $this->bindings;
    }

    /**
     * Batch add bindings.
     *
     * Called from the constructor if an array of bindings is passed in.
     *
     * @param array $bindings
     */
    private function addBindings($bindings)
    {
        foreach ($bindings as $binding) {
            $this->bind($binding[0], $binding[1], $binding[2]);
        }
    }

}
