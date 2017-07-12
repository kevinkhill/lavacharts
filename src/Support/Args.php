<?php

namespace Khill\Lavacharts\Support;

use ArrayAccess;
use Khill\Lavacharts\Exceptions\InvalidArgumentException;

class Args extends ArrayObject
{
    /**
     * Working args array
     *
     * @var array
     */
    protected $arr;

    /**
     * Create a new Arr object to work on the given array.
     *
     * @param array $arr
     *
     * @return static
     */
    public static function create($arr)
    {
        return new static($arr);
    }

    /**
     * Arr constructor.
     *
     * @param array $arr
     */
    public function __construct($arr)
    {
        if (! is_array($arr) && ! $arr instanceof ArrayAccess) {
            throw new InvalidArgumentException($arr, 'array | ArrayAccess implementation');
        }

        $this->arr = $arr;
    }

    /**
     * @inheritdoc
     */
    public function getArrayAccessProperty()
    {
        return 'arr';
    }

    public function exists($index)
    {
        return isset($this->arr[$index]);
    }

    public function notExists($index)
    {
        return ! $this->exists($index);
    }

    public function isNull($index)
    {
        return $this->exists($index) && is_null($this->arr[$index]);
    }

    public function isNotNull($index)
    {
        return ! $this->isNull($index);
    }

    /**
     * Test an array for index and type with a default return value.
     *
     * Verify that the working array has a value at the given index,
     * and that the value passes an "is_xxxxxxx" test and if that
     * check fails, return the default.
     *
     * @param int          $index
     * @param string|array $types
     * @param null|mixed   $default
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function verify($index, $types, $default = null)
    {
        $tests = [];

        if (! isset($this->arr[$index])) {
            return $default;
        }

        $types = is_string($types) ? [$types] : $types;

        if (is_array($types)) {
            foreach ($types as $type) {
                if ($this->is($index, $type)) {
                    $tests[] = true;
                } else {
                    $tests[] = false;
                }
            }

            $verified = array_reduce($tests, function ($prev, $curr) {
                return $prev || $curr;
            }, false);

            if (! $verified && ! isset($default)) {
                throw new InvalidArgumentException($this->arr[$index], join('|', $types));
            }

            if (isset($default)) {
                return $default;
            }
        }

        return $this->arr[$index];
    }

    /**
     * Test a value with an "is_xxxxx" function.
     *
     * @param int    $index
     * @param string $type
     * @return bool
     */
    public function is($index, $type)
    {
        $isCheck = "is_{$type}";

        return $isCheck($this->arr[$index]);
    }
}
