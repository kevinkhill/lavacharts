<?php

namespace Khill\Lavacharts\Dashboards\Wrappers;

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\Exceptions\InvalidChartType;

/**
 * ChartWrapper Class
 *
 * Used for wrapping charts to use in dashboards.
 *
 * @package   Khill\Lavacharts\Dashboards\Wrappers
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class ChartWrapper extends Wrapper
{
    /**
     * Builds a ChartWrapper object.
     *
     * @since 4.0.0 Instead of a Chart object, just the string name is needed.
     * @param string $chartType
     * @param string $containerId
     * @throws InvalidChartType
     */
    public function __construct($chartType, $containerId)
    {
        if (! in_array($chartType, ChartFactory::TYPES)) {
            throw new InvalidChartType($chartType);
        }

        parent::__construct(Chart::create($chartType), $containerId);
    }

    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return self::GOOGLE_VISUALIZATION . 'ChartWrapper';
    }
}
