<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * CategoryUI Object
 *
 * Customization for Category Filters in Dashboards.
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
class CategoryUI extends UI
{
    /**
     * Type of UI object
     *
     * @var string
     */
    const TYPE = 'CategoryUI';

    /**
     * Default options available.
     *
     * @var array
     */
    private $categoryDefaults = [
        'caption',
        'sortValues',
        'selectedValuesLayout',
        'allowNone',
        'allowMultiple',
        'allowTyping'
    ];

    /**
     * Creates a new CategoryUI object
     *
     * @param  array $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);
        $options->extend($this->categoryDefaults);

        parent::__construct($options, $config);
    }

    /**
     * The caption to display inside the value picker widget when no item is selected.
     *
     * @param  string $caption
     * @return \Khill\Lavacharts\Configs\UIs\CategoryUI
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function caption($caption)
    {
        return $this->setStringOption(__FUNCTION__, $caption);
    }

    /**
     * Whether the values to choose from should be sorted.
     *
     * @param  boolean $sortValues
     * @return \Khill\Lavacharts\Configs\UIs\CategoryUI
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function sortValues($sortValues)
    {
        return $this->setBoolOption(__FUNCTION__, $sortValues);
    }

    /**
     * How to display selected values, when multiple selection is allowed.
     *
     * Possible values are:
     *  - 'aside': selected values will display in a single text line next to the value picker widget,
     *  - 'below': selected values will display in a single text line below the widget,
     *  - 'belowWrapping': similar to below, but entries that cannot fit in the picker will wrap to a new line,
     *  - 'belowStacked': selected values will be displayed in a column below the widget.
     *
     * @param  string $selectedValuesLayout
     * @return \Khill\Lavacharts\Configs\UIs\CategoryUI
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function selectedValuesLayout($selectedValuesLayout)
    {
        $values = [
            'aside',
            'below',
            'belowWrapping',
            'belowStacked'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $selectedValuesLayout, $values);
    }

    /**
     * Whether the user is allowed not to choose any value.
     *
     * If false the user must choose at least one value from the available ones.
     * During control initialization, if the option is set to false and no selectedValues state is given,
     * the first value from the available ones is automatically selected.
     *
     * @param  boolean $allowNone
     * @return \Khill\Lavacharts\Configs\UIs\CategoryUI
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function allowNone($allowNone)
    {
        return $this->setBoolOption(__FUNCTION__, $allowNone);
    }

    /**
     * Whether multiple values can be selected, rather than just one.
     *
     * @param  boolean $allowMultiple
     * @return \Khill\Lavacharts\Configs\UIs\CategoryUI
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function allowMultiple($allowMultiple)
    {
        return $this->setBoolOption(__FUNCTION__, $allowMultiple);
    }

    /**
     * Allow typing in a text field for filtering.
     *
     * Whether the user is allowed to type in a text field to narrow down
     * the list of possible choices (via an autocompleter), or not.
     *
     * @param  boolean $allowTyping
     * @return \Khill\Lavacharts\Configs\UIs\CategoryUI
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function allowTyping($allowTyping)
    {
        return $this->setBoolOption(__FUNCTION__, $allowTyping);
    }
}
