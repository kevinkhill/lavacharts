<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\DataTables\DataTable;

/**
 * TreeMap Chart Class
 *
 * A visual representation of a data tree, where each node can have zero or more
 * children, and one parent (except for the root, which has no parents). Each
 * node is displayed as a rectangle, sized and colored according to values that
 * you assign. Sizes and colors are valued relative to all other nodes in the
 * graph. You can specify how many levels to display simultaneously, and
 * optionally to display deeper levels in a hinted fashion. If a node is a leaf
 * node, you can specify a size and color; if it is not a leaf, it will be
 * displayed as a bounding box for leaf nodes. The default behavior is to move
 * down the tree when a user left-clicks a node, and to move back up the tree
 * when a user right-clicks the graph.
 *
 * The total size of the graph is determined by the size of the containing
 * element that you insert in your page. If you have leaf nodes with names too
 * long to show, the name will be truncated with an ellipsis (...).
 *
 * @codeCoverageIgnore
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class TreeMap extends Chart
{
    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'TreeMap';

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
    const VIZ_PACKAGE = 'treemap';

    /**
     * Google's visualization class name.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.TreeMap';

    /**
     * Default configuration options for the chart.
     *
     * @var array
     */
    private $treeMapDefaults = [
        'fontColor',
        'fontFamily',
        'headerColor',
        'headerHeight',
        'headerHighlightColor',
        'maxColor',
        'maxDepth',
        'maxHighlightColor',
        'maxPostDepth',
        'maxColorValue',
        'midColor',
        'midHighlightColor',
        'minColor',
        'minHighlightColor',
        'minColorValue',
        'noColor',
        'noHighlightColor',
        'showScale',
        'showTooltips'
    ];

    /**
     * Builds a new TreeMapChart with the given label, datatable and options.
     *
     * @param  \Khill\Lavacharts\Values\Label         $chartLabel Identifying label for the chart.
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable DataTable used for the chart.
     * @param array                                   $config
     */
    public function __construct(Label $chartLabel, DataTable $datatable, $config = [])
    {
        $options = new Options($this->treeMapDefaults);

        parent::__construct($chartLabel, $datatable, $options, $config);
    }

    /**
     * The color of the header section for each node. Specify an HTML color value.
     *
     * @param  string $headerColor
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function headerColor($headerColor)
    {
        return $this;
    }

    /**
     * The height of the header section for each node, in pixels (can be zero).
     *
     * @param  integer $headerHeight
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function headerHeight($headerHeight)
    {
        return $this;
    }

    /**
     * The color of the header of a node being hovered over. Specify an HTML
     * color value or null; if null this value will be headerColor lightened
     * by 35%
     *
     * @param  string $headerHighlightColor
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function headerHighlightColor($headerHighlightColor)
    {
        return $this;
    }

    /**
     * The color for a rectangle with a column 3 value of maxColorValue.
     * Specify an HTML color value.
     *
     * @param  string $maxColor
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function maxColor($maxColor)
    {
        return $this;
    }

    /**
     * The maximum number of node levels to show in the current view. Levels
     * will be flattened into the current plane. If your tree has more levels
     * than this, you will have to go up or down to see them. You can
     * additionally see maxPostDepth levels below this as shaded rectangles
     * within these nodes.
     *
     * @param  integer $maxDepth
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function maxDepth($maxDepth)
    {
        return $this;
    }

    /**
     * The highlight color to use for the node with the largest value in
     * column 3. Specify an HTML color value or null; If null, this value
     * will be the value of maxColor lightened by 35%.
     *
     * @param  string $maxHighlightColor
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function maxHighlightColor($maxHighlightColor)
    {
        return $this;
    }

    /**
     * How many levels of nodes beyond maxDepth to show in "hinted" fashion.
     * Hinted nodes are shown as shaded rectangles within a node that is within
     * the maxDepth limit.
     *
     * @param  integer $maxPostDepth
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function maxPostDepth($maxPostDepth)
    {
        return $this;
    }

    /**
     * The maximum value allowed in column 3. All values greater than this will
     * be trimmed to this value. If set to null, it will be set to the max value
     * in the column.
     *
     * @param  integer $maxColorValue
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function maxColorValue($maxColorValue)
    {
        return $this;
    }

    /**
     * The color for a rectangle with a column 3 value midway between
     * maxColorValue and minColorValue. Specify an HTML color value.
     *
     * @param  string $midColor
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function midColor($midColor)
    {
        return $this;
    }

    /**
     * The highlight color to use for the node with a column 3 value near the
     * median of minColorValue and maxColorValue. Specify an HTML color value
     * or null; if null, this value will be the value of midColor lightened
     * by 35%.
     *
     * @param  string $midHighlightColor
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function midHighlightColor($midHighlightColor)
    {
        return $this;
    }

    /**
     * The color for a rectangle with the column 3 value of minColorValue.
     * Specify an HTML color value.
     *
     * @param  string $minColor
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function minColor($minColor)
    {
        return $this;
    }

    /**
     * The highlight color to use for the node with a column 3 value nearest to
     * minColorValue. Specify an HTML color value or null; if null, this value
     * will be the value of minColor lightened by 35%.
     *
     * @param  string $minHighlightColor
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function minHighlightColor($minHighlightColor)
    {
        return $this;
    }

    /**
     * The minimum value allowed in column 3. All values less than this will be
     * trimmed to this value. If set to null, it will be calculated as the
     * minimum value in the column.
     *
     * @param  integer $minColorValue
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function minColorValue($minColorValue)
    {
        return $this;
    }

    /**
     * The color to use for a rectangle when a node has no value for column 3,
     * and that node is a leaf (or contains only leaves). Specify an HTML
     * color value.
     *
     * @param  string $noColor
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function noColor($noColor)
    {
        return $this;
    }

    /**
     * The color to use for a rectangle of "no" color when highlighted. Specify
     * an HTML color value or null; if null, this will be the value of noColor
     * lightened by 35%.
     *
     * @param  string $noHighlightColor
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function noHighlightColor($noHighlightColor)
    {
        return $this;
    }

    /**
     * Whether or not to show a color gradient scale from minColor to maxColor
     * along the top of the chart. Specify true to show the scale.
     *
     * @param  bool $showScale
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function showScale($showScale)
    {
        return $this;
    }

    /**
     * Whether or not to show tooltips.
     *
     * @param  bool $showTooltips
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function showTooltips($showTooltips)
    {
        return $this;
    }

    /**
     * The text color. Specify an HTML color value.
     *
     * @param  string $fontColor
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function fontColor($fontColor)
    {
        return $this;
    }

    /**
     * The font family to use for all text.
     *
     * @param  string $fontFamily
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function fontFamily($fontFamily)
    {
        return $this;
    }

    /**
     * The font size for all text, in points.
     *
     * @param  integer $fontSize
     * @return \Khill\Lavacharts\Charts\TreeMap
     */
    public function fontSize($fontSize)
    {
        return $this;
    }
}
