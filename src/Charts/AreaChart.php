<?php namespace Khill\Lavacharts\Charts;

/**
 * AreaChart Class
 *
 * An area chart that is rendered within the browser using SVG or VML. Displays
 * tips when hovering over points.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @since      v1.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Utils;

class AreaChart extends Chart
{
    use \Khill\Lavacharts\Traits\AnimationTrait;
    use \Khill\Lavacharts\Traits\AreaOpacityTrait;
    use \Khill\Lavacharts\Traits\AxisTitlesPositionTrait;
    use \Khill\Lavacharts\Traits\FocusTargetTrait;
    use \Khill\Lavacharts\Traits\HorizontalAxisTrait;
    use \Khill\Lavacharts\Traits\IsStackedTrait;
    use \Khill\Lavacharts\Traits\InterpolateNullsTrait;
    use \Khill\Lavacharts\Traits\LineWidthTrait;
    use \Khill\Lavacharts\Traits\PointSizeTrait;
    use \Khill\Lavacharts\Traits\VerticalAxisTrait;

    //const TYPE = 'AreaChart';
    public $type = 'AreaChart';

    private $extraOptions = [
        'animation',
        'areaOpacity',
        'axisTitlesPosition',
        'focusTarget',
        'hAxis',
        'isHtml',
        'interpolateNulls',
        'lineWidth',
        'pointSize',
        //'vAxes',
        'vAxis'
    ];

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel, $this->extraOptions);
    }

}
