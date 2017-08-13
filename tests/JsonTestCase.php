<?php

namespace Khill\Lavacharts\Tests;

use JsonSchema\Validator;

/**
 * Class JsonTestCase
 *
 * To test the various aspects of the new json output format of Lavacharts,
 * I have borrowed some code from the wonderful Laravel framework and
 * tailored it for my own needs.
 *
 * @author Taylor Otwell <https://github.com/taylorotwell>
 * @link https://github.com/laravel/framework/blob/5.4/src/Illuminate/Foundation/Testing/TestResponse.php
 * @link https://github.com/laravel/framework/blob/5.4/src/Illuminate/Support/Arr.php
 * @link https://github.com/laravel/framework/blob/5.4/src/Illuminate/Support/Str.php
 */
class JsonTestCase extends ProvidersTestCase
{
    /**
     * @var Validator
     */
    private $validator;

    public function setUp()
    {
        parent::setUp();

        $this->validator = new Validator();
    }

    public function assertValidJsonWithSchema($data, $pathToSchema)
    {
        $schema = (object) [
            '$ref' => 'file://' . realpath(__DIR__.'/'.$pathToSchema)
        ];

        $this->validator->check($data, $schema);

        $errorMsg = '';

        if (! $this->validator->isValid()) {
            foreach ($this->validator->getErrors() as $error) {
                $errorMsg .= sprintf("[%s] %s.\n", $error['property'], $error['message']);
            }
        }

        $this->assertTrue($this->validator->isValid(),  $errorMsg);
    }

    /**
     * Assert that the data is a superset of the given JSON.
     *
     * @param  array  $data
     * @return $this
     */
    public function assertIsJson(array $data)
    {
        $this->assertArraySubset(
            $data, $this->decodeJson(), false, $this->assertJsonMessage($data)
        );

        return $this;
    }

    /**
     * Get the assertion message for assertJson.
     *
     * @param  array  $data
     * @return string
     */
    protected function assertJsonMessage(array $data)
    {
        $expected = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $actual = json_encode($this->decodeJson(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return 'Unable to find JSON: '.PHP_EOL.PHP_EOL.
            "[{$expected}]".PHP_EOL.PHP_EOL.
            'within response JSON:'.PHP_EOL.PHP_EOL.
            "[{$actual}].".PHP_EOL.PHP_EOL;
    }

    /**
     * Assert that the response has the exact given JSON.
     *
     * @param  array  $data
     * @return $this
     */
    public function assertExactJson(array $data)
    {
        $actual = json_encode($this->sortRecursive(
            (array) $this->decodeJson()
        ));

        $this->assertEquals(json_encode($this->sortRecursive($data)), $actual);

        return $this;
    }

    /**
     * Assert that the response contains the given JSON fragment.
     *
     * @param  array  $data
     * @return $this
     */
    public function assertJsonFragment(array $data)
    {
        $actual = json_encode($this->sortRecursive(
            (array) $this->decodeJson()
        ));

        foreach ($this->sortRecursive($data) as $key => $value) {
            $expected = substr(json_encode([$key => $value]), 1, -1);

            $this->assertTrue(
                $this->strContains($actual, $expected),
                'Unable to find JSON fragment: '.PHP_EOL.PHP_EOL.
                "[{$expected}]".PHP_EOL.PHP_EOL.
                'within'.PHP_EOL.PHP_EOL.
                "[{$actual}]."
            );
        }

        return $this;
    }

    /**
     * Assert that the response does not contain the given JSON fragment.
     *
     * @param  array  $data
     * @return $this
     */
    public function assertJsonMissing(array $data)
    {
        $actual = json_encode($this->sortRecursive(
            (array) $this->decodeJson()
        ));

        foreach ($this->sortRecursive($data) as $key => $value) {
            $expected = substr(json_encode([$key => $value]), 1, -1);

            $this->assertFalse(
                $this->strContains($actual, $expected),
                'Found unexpected JSON fragment: '.PHP_EOL.PHP_EOL.
                "[{$expected}]".PHP_EOL.PHP_EOL.
                'within'.PHP_EOL.PHP_EOL.
                "[{$actual}]."
            );
        }

        return $this;
    }

    /**
     * Assert that the response has a given JSON structure.
     *
     * @param  array|null  $structure
     * @param  array|null  $jsonData
     * @return $this
     */
    public function assertJsonStructure(array $structure = null, $jsonData = null)
    {
        if (is_null($structure)) {
            return $this->assertIsJson($this->decodeJson());
        }

        if (is_null($jsonData)) {
            $jsonData = $this->decodeJson();
        }

        foreach ($structure as $key => $value) {
            if (is_array($value) && $key === '*') {
                $this->assertInternalType('array', $jsonData);

                foreach ($jsonData as $jsonDataItem) {
                    $this->assertJsonStructure($structure['*'], $jsonDataItem);
                }
            } elseif (is_array($value)) {
                $this->assertArrayHasKey($key, $jsonData);

                $this->assertJsonStructure($structure[$key], $jsonData[$key]);
            } else {
                $this->assertArrayHasKey($value, $jsonData);
            }
        }

        return $this;
    }

    /**
     * Validate and return the decoded response JSON.
     *
     * @return array
     */
    public function decodeJson()
    {
        $decodedJson = json_decode($this->getJson(), true);

        if (is_null($decodedJson) || $decodedJson === false) {
            $this->fail('Invalid JSON was returned from getContent().');
        }

        return $decodedJson;
    }

    /**
     * Determines if an array is associative.
     *
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     *
     * @param  array  $array
     * @return bool
     */
    protected function isAssoc(array $array)
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * Recursively sort an array by keys and values.
     *
     * @param  array  $array
     * @return array
     */
    protected function sortRecursive($array)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = $this->sortRecursive($value);
            }
        }

        if ($this->isAssoc($array)) {
            ksort($array);
        } else {
            sort($array);
        }

        return $array;
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    private function strContains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}
