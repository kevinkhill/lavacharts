<?php

namespace \Khill\Lavacharts\Dashboard;

use \Khill\Lavacharts\Dashboard\Filters\Filter;

class ControlWrapper {

    /**
     * Javascript chart class.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.ControlWrapper';

    /**
     * Array of options for the Control.
     *
     * @var array
     */
    private $options;

    /**
     * Filter used in the Control.
     *
     * @var \Khill\Lavacharts\Dashboard\Filters\Filter
     */
    private $controlType;

    /**
     * ContainerId of the div to render the control into.
     *
     * @var string
     */
    private $containerId;

    /**
     * Builds a ControlWrapper object.
     *
     * @param  \Khill\Lavacharts\Charts\Chart $chart
     * @param  \Khill\Lavacharts\Dashboard\Filters\Filter $filter
     * @param  string $containerId
     * @return self
     */
    public function __construct(Filter $filter, $containerId)
    {
        if (Utils::nonEmptyString($containerId) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        $this->controlType = $filter;
        $this->containerId = $containerId;
    }
}
