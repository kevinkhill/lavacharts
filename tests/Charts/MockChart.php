<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Charts\Chart;

/**
 * MockChart Class
 *
 * This is used to apply all the traits for testing, as well as testing the parent methods for all the charts.
 */
class MockChart extends Chart
{
    const TYPE = 'MockChart';

    const VERSION = '1';

    const VIZ_PACKAGE = 'mockchart';

    const VIZ_CLASS = 'google.visualization.MockChart';
}
