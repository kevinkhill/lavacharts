<?php

namespace Khill\Lavacharts\Dashboards;

use \Khill\Lavacharts\Values\ElementId;
use \Khill\Lavacharts\Dashboards\Filters\Filter;

/**
 * ControlWrapper Class
 *
 * Used for building controls for dashboards.
 *
 * @package    Lavacharts
 * @subpackage Dashboards
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class ControlWrapper extends Wrapper implements \JsonSerializable
{
    /**
     * Google's visualization class name.
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
     * @var \Khill\Lavacharts\Dashboards\Filters\Filter
     */
    private $controlType;

    /**
     * Builds a ControlWrapper object.
     *
     * @param  \Khill\Lavacharts\Charts\Chart $chart
     * @param  \Khill\Lavacharts\Dashboards\Filters\Filter $filter
     * @param  string $containerId
     * @return self
     */
    public function __construct(Filter $filter, ElementId $containerId)
    {
        parent::__construct($containerId);

        $this->type        = $filter::TYPE;
        $this->controlType = $filter;
    }

    /**
     * Custom serialization of the ControlWrapper.
     *
     * @return array
     */
    public function jsonSerialize() {
        return [
            'controlType' => $this->type,
            'containerId' => (string) $this->containerId,
            'options' => [ //@TODO: make options classes
                'filterColumnLabel' => $this->controlType->columnLabel,
                /*'ui' => [
                    'labelStacking' => 'vertical'
                ]*/
            ]
        ];
    }
}
