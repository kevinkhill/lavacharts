<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

class CategoryUI extends UI
{
    /**
     * Default options available.
     *
     * @var array
     */
    private $extDefaults = [
        'caption',
        'sortValues',
        'selectedValuesLayout',
        'allowNone',
        'allowMultiple',
        'allowTyping'
    ];

    public function __construct($config = [])
    {
        $options = new Options($this->defaults);
        $options->extend($this->extDefaults);

        parent::__construct($options, $config);
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
        if (Utils::nonEmptyString($caption) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this->setOption(__FUNCTION__, $caption);
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
        if (is_bool($sortValues) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'boolean'
            );
        }

        return $this->setOption(__FUNCTION__, $sortValues);
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
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function selectedValuesLayout($selectedValuesLayout)
    {
        $values = [
            'aside',
            'below',
            'belowWrapping',
            'belowStacked'
        ];

        if (Utils::nonEmptyStringInArray($selectedValuesLayout, $values) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string',
                ' whose accepted values are '.Utils::arrayToPipedString($values)
            );
        }

        return $this->setOption(__FUNCTION__, $selectedValuesLayout);
    }

    /**
     * Whether the user is allowed not to choose any value.
     *
     * If false the user must choose at least one value from the available ones.
     * During control initialization, if the option is set to false and no selectedValues state is given,
     * the first value from the available ones is automatically selected.
     *
     * @param  boolean $allowNone
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function allowNone($allowNone)
    {
        if (is_bool($allowNone) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'boolean'
            );
        }

        return $this->setOption(__FUNCTION__, $allowNone);
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
        if (is_bool($allowMultiple) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'boolean'
            );
        }

        return $this->setOption(__FUNCTION__, $allowMultiple);
    }

    /**
     * Allow typing in a text field for filtering.
     *
     * Whether the user is allowed to type in a text field to narrow down
     * the list of possible choices (via an autocompleter), or not.
     *
     * @param  boolean $allowTyping
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function allowTyping($allowTyping)
    {
        if (is_bool($allowTyping) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'boolean'
            );
        }

        return $this->setOption(__FUNCTION__, $allowTyping);
    }

}
