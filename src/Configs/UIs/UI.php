<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\JsonConfig;

/**
 * UI Object
 *
 * The parent object for all UI config objects. Adds JsonSerializable and methods for setting options.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs\UIs
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class UI extends JsonConfig
{
    /**
     * Default options available.
     *
     * @var array
     */
    protected $defaults = [
        'label',
        'labelSeparator',
        'labelStacking',
        'cssClass'
    ];

    /**
     * Returns the UI object type
     *
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * The label to display next to the category picker.
     *
     * If unspecified, the label of the column the control operates on will be used.
     *
     * @param  string $label Label to display
     * @return \Khill\Lavacharts\Configs\UIs\UI
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function label($label)
    {
        return $this->setStringOption(__FUNCTION__, $label);
    }

    /**
     * A separator string appended to the label, to visually separate the label from the control.
     *
     * @param  string $labelSeparator
     * @return \Khill\Lavacharts\Configs\UIs\UI
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function labelSeparator($labelSeparator)
    {
        return $this->setStringOption(__FUNCTION__, $labelSeparator);
    }

    /**
     * Whether the label should display above or beside the control.
     *
     * Accepted values:
     *  - 'vertical'
     *  - 'horizontal'
     *
     * @param  string $labelStacking
     * @return \Khill\Lavacharts\Configs\UIs\UI
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function labelStacking($labelStacking)
    {
        $values = [
            'vertical',
            'horizontal'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $labelStacking, $values);
    }

    /**
     * The CSS class to assign to the control, for custom styling.
     *
     * @param  string $cssClass
     * @return \Khill\Lavacharts\Configs\UIs\UI
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function cssClass($cssClass)
    {
        return $this->setStringOption(__FUNCTION__, $cssClass);
    }
}
