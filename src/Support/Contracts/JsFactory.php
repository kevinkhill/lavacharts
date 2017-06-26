<?php

namespace Khill\Lavacharts\Support\Contracts;

use Khill\Lavacharts\Javascript\ChartJsFactory;
use Khill\Lavacharts\Javascript\DashboardJsFactory;

/**
 * JsFactory Interface
 *
 * Defines what type of JsFactory will be returned.
 *
 *
 * @package   Khill\Lavacharts\Support\Contracts
 * @since     3.2.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
interface JsFactory
{
    /**
     * Returns the DataTable
     *
     * @since  3.0.0
     * @return ChartJsFactory|DashboardJsFactory
     */
    public function getJsFactory();
}
