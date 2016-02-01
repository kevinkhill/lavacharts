<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\DataTables\DataTable;

/**
 * SankeyChart Class
 *
 * A sankey diagram is a visualization used to depict a flow from one set
 * of values to another. The things being connected are called nodes and
 * the connections are called links.
 *
 * Sankeys are best used when you want to show a many-to-many mapping
 * between two domains (e.g., universities and majors) or multiple paths
 * through a set of stages (for instance, Google Analytics uses sankeys
 * to show how traffic flows from pages to other pages on your web site).
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @since      3.0.1
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2016, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class SankeyChart extends Chart
{
    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'SankeyChart';

    /**
     * Javascript chart version.
     *
     * @var string
     */
    const VERSION = '1';

    /**
     * Javascript chart package.
     *
     * @var string
     */
    const VIZ_PACKAGE = 'sankey';

    /**
     * Google's visualization class name.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.Sankey';
}
