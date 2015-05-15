<?php

namespace Khill\Lavacharts\Configs\ControlUIs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\ConfigObject;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * UI ConfigObject
 *
 * An object containing the UI properties for the chart.
 *
 *
 * @package    Lavacharts
 * @subpackage Configs\ControlUIs
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 * @see        https://developers.google.com/chart/interactive/docs/gallery/controls?csw=1#categoryfilter
 */
class CategoryUI extends ConfigObject
{
    /**
     * [$label description]
     * 
     * @var string
     */
    public $caption;
    
    /**
     * [$sortValues description]
     * 
     * @var boolean
     */
    public $sortValues;
    
    /**
     * [$label description]
     * 
     * @var string
     */
    public $selectedValuesLayout;
    
    /**
     * [$sortValues description]
     * 
     * @var boolean
     */
    public $allowNone;

    /**
     * [$sortValues description]
     * 
     * @var boolean
     */    
    public $allowMultiple;

    /**
     * [$sortValues description]
     * 
     * @var boolean
     */    
    public $allowTyping;

    /**
     * [$label description]
     * 
     * @var string
     */
    public $label;

    /**
     * [$label description]
     * 
     * @var string
     */
    public $labelSeparator;

    /**
     * [$label description]
     * 
     * @var string
     */
    public $labelStacking;
    
    /**
     * [$label description]
     * 
     * @var string
     */
    public $cssClass;


    /**
     * Stores all the information about the UI.
     *
     * All options can be set either by passing an array with associative
     * values for option => value, or by chaining together the functions
     * once an object has been created.
     *
     * @param  array                 $config
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return self
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);

        $this->options = array_merge(
            $this->options,
            array(
                'caption',
                'sortValues',
                'selectedValuesLayout',
                'allowNone',
                'allowMultiple',
                'allowTyping',
                'label',
                'labelSeparator',
                'labelStacking',
                'cssClass'
            )
        );
    }

    /**
     * The caption to display inside the value picker widget when no item is selected.
     *
     * @param  string $caption
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function caption($caption)
    {
        if (Utils::nonEmptyString($caption)) {
            $this->caption = $caption;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Whether the values to choose from should be sorted.
     *
     * @param  boolean $sortValues
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function sortValues($sortValues)
    {
        if (is_bool($sortValues)) {
            $this->sortValues = $sortValues;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

    /**
     * How to display selected values, when multiple selection is allowed.
     * 
     * Possible values are:
     *  'aside': selected values will display in a single text line next to the value picker widget,
     *  'below': selected values will display in a single text line below the widget,
     *  'belowWrapping': similar to below, but entries that cannot fit in the picker will wrap to a new line,
     *  'belowStacked': selected values will be displayed in a column below the widget.
     *
     * @param  string $selectedValuesLayout
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function selectedValuesLayout($selectedValuesLayout)
    {
        $values = array(
            'aside',
            'below',
            'belowWrapping',
            'belowStacked'
        );

        if (in_array($selectedValuesLayout, $values)) {
            $this->selectedValuesLayout = $selectedValuesLayout;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }

    /**
     * Whether the user is allowed not to choose any value.
     * 
     * If false the user must choose at least one value from the available ones.
     * During control initialization, if the option is set to false and no 
     * selectedValues state is given, the first value from the avaiable ones
     * is automatically seleted.
     *
     * @param  boolean $allowNone
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function allowNone($allowNone)
    {
        if (is_bool($allowNone)) {
            $this->allowNone = $allowNone;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

    /**
     * Whether multiple values can be selected, rather than just one.
     * 
     * @param  boolean $allowMultiple
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function allowMultiple($allowMultiple)
    {
        if (is_bool($allowMultiple)) {
            $this->allowMultiple = $allowMultiple;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

    /**
     * Whether the user is allowed to type in a text field to narrow
     * down the list of possible choices (via an autocompleter), or not.
     * 
     * @param  boolean $allowTyping
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function allowTyping($allowTyping)
    {
        if (is_bool($allowTyping)) {
            $this->allowTyping = $allowTyping;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this;
    }

    /**
     * The label to display next to the category picker.
     * 
     * If unspecified, the label of the column the control operates on will be used.
     *
     * @param  string $label
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function label($label)
    {
        if (Utils::nonEmptyString($label)) {
            $this->label = $label;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * A separator string appended to the label, to visually separate
     * the label from the category picker.
     * 
     * @param  string $labelSeparator
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function labelSeparator($labelSeparator)
    {
        if (Utils::nonEmptyString($labelSeparator)) {
            $this->labelSeparator = $labelSeparator;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Whether the label should display above (vertical stacking) or beside (horizontal stacking)
     * the category picker. Use either 'vertical' or 'horizontal'.
     * 
     * @param  string $labelStacking
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function labelStacking($labelStacking)
    {
        $values = array(
            'aside',
            'below',
            'belowWrapping',
            'belowStacked'
        );

        if (in_array($labelStacking, $values)) {
            $this->labelStacking = $labelStacking;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this;
    }

    /**
     * The CSS class to assign to the control, for custom styling.
     * 
     * @param  string $cssClass
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function cssClass($cssClass)
    {
        if (Utils::nonEmptyString($cssClass)) {
            $this->cssClass = $cssClass;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }
}