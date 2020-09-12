<?php

namespace Khill\Lavacharts\Dashboards\Wrappers;

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\Exceptions\InvalidChartType;
use Khill\Lavacharts\Support\Contracts\Customizable;
use Khill\Lavacharts\Support\Traits\HasOptionsTrait as HasOptions;

/**
 * ChartWrapper Class
 *
 * Used for wrapping charts to use in dashboards.
 *
 * @package   Khill\Lavacharts\Dashboards\Wrappers
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class ChartWrapper extends Wrapper implements Customizable
{
    use HasOptions;

    /**
     * Builds a ChartWrapper object.
     *
     * @since 4.0.0 String chart names are allowed along with Chart objects.
     * @param Chart|string $chartType
     * @param string $containerId
     * @throws InvalidChartType
     */
    public function __construct($chartType, $containerId) //TODO: add options to the signature
    {
        if ($chartType instanceof Chart) {
            $chartType = $chartType->getType();
        }

        if (! in_array($chartType, ChartFactory::TYPES, true)) {
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
