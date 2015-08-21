<?php

namespace Khill\Lavacharts\Configs\UIs;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Exceptions\InvalidUIProperty;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

class UI implements \JsonSerializable
{
    /**
     * Allowed options to set for the UI.
     *
     * @var \Khill\Lavacharts\Options
     */
    protected $options;

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

    public function __construct(Options $options, $config = [])
    {
        $this->options = $options;

        if (is_array($config) === true && empty($config) === false) {
            $this->parseConfig($config);
        }
    }

    /**
     * Get the value of a set option via magic method through UI.
     *
     * @param  string $option Name of option.
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return mixed
     */
    public function __get($option)
    {
        return $this->options->get($option);
    }

    private function parseConfig($config)
    {
        foreach ($config as $option => $value) {
            if ($this->options->hasOption($option) === false) {
                throw new InvalidUIProperty(
                    $option,
                    $this->options->getDefaults()
                );
            }

            call_user_func([$this, $option], $value);
        }
    }

    /**
     * Sets the value of an option.
     *
     * Used internally to check the values for their respective types and validity.
     *
     * @param  string $option Option to set.
     * @param  mixed $value Value of the option.
     * @return self
     */
    protected function setOption($option, $value)
    {
        $this->options->set($option, $value);

        return $this;
    }

    public function getType() //TODO: look at this again
    {
        return static::$TYPE;
    }

    /**
     * Gets the Options object for the UI
     *
     * @return \Khill\Lavacharts\Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * The label to display next to the category picker.
     *
     * If unspecified, the label of the column the control operates on will be used.
     *
     * @param  string $label Label to display
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function label($label)
    {
        if (Utils::nonEmptyString($label) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this->setOption(__FUNCTION__, $label);
    }

    /**
     * A separator string appended to the label, to visually separate the label from the control.
     *
     * @param string $labelSeparator
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function labelSeparator($labelSeparator)
    {
        if (Utils::nonEmptyString($labelSeparator) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this->setOption(__FUNCTION__, $labelSeparator);
    }

    /**
     * Whether the label should display above or beside the control.
     *
     * Accepted values:
     *  - 'vertical'
     *  - 'horizontal'
     *
     * @param string $cssClass
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function labelStacking($labelStacking)
    {
        $values = [
            'vertical',
            'horizontal'
        ];

        if (Utils::nonEmptyStringInArray($labelStacking, $values) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string',
                ' which must be one of '.Utils::arrayToPipedString($values)
            );
        }

        return $this->setOption(__FUNCTION__, $labelStacking);
    }

    /**
     * The CSS class to assign to the control, for custom styling.
     *
     * @param string $cssClass
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return self
     */
    public function cssClass($cssClass)
    {
        if (Utils::nonEmptyString($cssClass) === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this->setOption(__FUNCTION__, $cssClass);
    }

    /**
     * Custom serialization of the UI object.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->options->getValues();
    }
}
