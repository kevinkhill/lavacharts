<?php

namespace Khill\Lavacharts\Dashboard;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Dashboard\Binding;
use \Khill\Lavacharts\Exceptions\InvalidLabel;

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
     * @var string
     */
    public $label = null;

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
    public function __construct($label)
    {
        if (Utils::nonEmptyString($label) === false) {
            throw new InvalidLabel($label);
        }

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
     * Custom JSON serialization of the Dashboard.
     *
     * @return string JSON
     */
    public function jsonSerialize()
    {
        return $this->bindings;
    }
}
