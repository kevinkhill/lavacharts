<?php namespace Khill\Lavacharts\Charts;

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
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * @codeCoverageIgnore
 */
class TreeMap extends Chart
{
    public $type = 'TreeMap';

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array(
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
            'showTooltips',
            'fontColor',
            'fontFamily',
            'fontSize'
        );
    }

    /**
     * The color of the header section for each node. Specify an HTML color value.
     *
     * @param string $headerColor
     *
     * @return TreeMap
     */
    public function headerColor($headerColor)
    {
        return $this;
    }

    /**
     * The height of the header section for each node, in pixels (can be zero).
     *
     * @param int $headerHeight
     *
     * @return TreeMap
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
     * @param string $headerHighlightColor
     *
     * @return TreeMap
     */
    public function headerHighlightColor($headerHighlightColor)
    {
        return $this;
    }

    /**
     * The color for a rectangle with a column 3 value of maxColorValue.
     * Specify an HTML color value.
     *
     * @param string $maxColor
     *
     * @return TreeMap
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
     * @param int $maxDepth
     *
     * @return TreeMap
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
     * @param string $maxHighlightColor
     *
     * @return TreeMap
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
     * @param int $maxPostDepth
     *
     * @return TreeMap
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
     * @param int $maxColorValue
     *
     * @return TreeMap
     */
    public function maxColorValue($maxColorValue)
    {
        return $this;
    }

    /**
     * The color for a rectangle with a column 3 value midway between
     * maxColorValue and minColorValue. Specify an HTML color value.
     *
     * @param string $midColor
     *
     * @return TreeMap
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
     * @param string $midHighlightColor
     *
     * @return TreeMap
     */
    public function midHighlightColor($midHighlightColor)
    {
        return $this;
    }

    /**
     * The color for a rectangle with the column 3 value of minColorValue.
     * Specify an HTML color value.
     *
     * @param string $minColor
     *
     * @return TreeMap
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
     * @param string $minHighlightColor
     *
     * @return TreeMap
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
     * @param int $minColorValue
     *
     * @return TreeMap
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
     * @param string $noColor
     *
     * @return TreeMap
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
     * @param string $noHighlightColor
     *
     * @return TreeMap
     */
    public function noHighlightColor($noHighlightColor)
    {
        return $this;
    }

    /**
     * Whether or not to show a color gradient scale from minColor to maxColor
     * along the top of the chart. Specify true to show the scale.
     *
     * @param bool $showScale
     *
     * @return TreeMap
     */
    public function showScale($showScale)
    {
        return $this;
    }

    /**
     * Whether or not to show tooltips.
     *
     * @param bool $showTooltips
     *
     * @return TreeMap
     */
    public function showTooltips($showTooltips)
    {
        return $this;
    }

    /**
     * The text color. Specify an HTML color value.
     *
     * @param string $fontColor
     *
     * @return TreeMap
     */
    public function fontColor($fontColor)
    {
        return $this;
    }

    /**
     * The font family to use for all text.
     *
     * @param string $fontFamily
     *
     * @return TreeMap
     */
    public function fontFamily($fontFamily)
    {
        return $this;
    }

    /**
     * The font size for all text, in points.
     *
     * @param int $fontSize
     *
     * @return TreeMap
     */
    public function fontSize($fontSize)
    {
        return $this;
    }
}
