<?php

namespace Khill\Lavacharts\Support;

use ReflectionClass;
use BadMethodCallException;
use InvalidArgumentException;

/**
 * AbstractBuilder Class
 *
 * The AbstractBuilder is used as a base for other builders.
 *
 *
 * @package       Khill\Lavacharts\Support
 * @since         4.0.0
 * @author        TheCelavi <https://pastebin.com/u/TheCelavi>
 * @link          http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link          http://lavacharts.com                   Official Docs Site
 * @license       http://opensource.org/licenses/MIT      MIT
 */
abstract class AbstractBuilder
{
    protected $data;

    public function __construct()
    {
        $this->data = $this->configureParameters();
    }

    public function build()
    {
        $reflector = new ReflectionClass($this->getObjectFqcn());

        return $reflector->newInstanceArgs(array_values($this->data));
    }

    public function setValues(array $values)
    {
        foreach ($values as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    public function __set($name, $value)
    {
        if ( ! $this->__isset($name)) {
            throw new InvalidArgumentException(
                sprintf('Unknown property "%s" in "%s".', $name, get_class($this))
            );
        }

        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        if ( ! $this->__isset($name)) {
            throw new InvalidArgumentException(
                sprintf('Unknown property "%s" in "%s".', $name, get_class($this))
            );
        }

        return $this->data[$name];
    }

    /**
     * Check if a property is set.
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }

    public function __call($name, array $arguments)
    {
        $property = lcfirst(substr($name, 3));

        if (strpos($name, 'set') !== 0 || ! $this->__isset($property)) {
            throw new BadMethodCallException(
                sprintf('Unknown method "%s" in "%s".', $name, get_class($this))
            );
        }

        if (count($arguments) !== 1) {
            throw new BadMethodCallException(
                sprintf('Method "%s" in "%s" expects exactly one parameter.', $name, get_class($this))
            );
        }

        $this->data[$property] = $arguments[0];

        return $this;
    }

    /**
     * Get a new instance of the
     *
     * @return static
     */
    public static function createBuilder()
    {
        return new static();
    }

    /**
     * Configure builder parameters that will be passed to building class constructor.
     *
     * @return array
     */
    protected abstract function configureParameters();

    /**
     * Get full qualified class name of class that instance ought to be constructed.
     *
     * @return string
     */
    protected abstract function getObjectFqcn();
}
