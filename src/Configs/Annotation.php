<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;

/**
 * Annotation ConfigObject
 *
 * An object containing all the values for the annotation which can
 * be passed into the chart's options.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class Annotation extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'Annotation';

    /**
     * Default options for Annotation
     *
     * @var array
     */
    private $defaults = [
        'alwaysOutside',
        'highContrast',
        'textStyle'
    ];

    /**
     * Builds the Annotation object.
     *
     * @param  array $config Associative array containing key => value pairs for the various configuration options.
     * @return \Khill\Lavacharts\Configs\Annotation
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * In Bar and Column charts, if set to true, draws all annotations outside of the Bar/Column.
     *
     * @param  bool $alwaysOutside
     * @return \Khill\Lavacharts\Configs\Annotation
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function alwaysOutside($alwaysOutside)
    {
        return $this->setBoolOption(__FUNCTION__, $alwaysOutside);
    }

    /**
     * For charts that support annotations, the highContrast bool lets you override Google Charts'
     * choice of the annotation color. By default, highContrast is true, which causes Charts to select
     * an annotation color with good contrast: light colors on dark backgrounds, and dark on light.
     *
     * If you set highContrast to false and don't specify your own annotation color, Google Charts
     * will use the default series color for the annotation
     *
     * @param  bool $highContrast
     * @return \Khill\Lavacharts\Configs\Annotation
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function highContrast($highContrast)
    {
        return $this->setBoolOption(__FUNCTION__, $highContrast);
    }

    /**
     * An object that specifies the annotation text style.
     *
     * @param  array $textStyleConfig Style of the annotation
     * @return \Khill\Lavacharts\Configs\Annotation
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function textStyle($textStyleConfig)
    {
        return $this->setOption(__FUNCTION__, new TextStyle($textStyleConfig));
    }
}
