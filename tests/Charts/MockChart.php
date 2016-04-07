<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Options;
use Khill\Lavacharts\Values\Label;
use Khill\Lavacharts\DataTables\DataTable;

/**
 * MockChart Class
 *
 * This is used to apply all the traits for testing, as well as testing the parent methods for all the charts.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class MockChart extends Chart
{
    const TYPE = 'MockChart';

    const VERSION = '1';

    const VIZ_PACKAGE = 'mockchart';

    const VIZ_CLASS = 'google.visualization.MockChart';
}
