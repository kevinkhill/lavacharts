<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Support\Buffer;

/**
 * NodeModule Class
 *
 * Used for building <script> tags
 *
 * @package   Khill\Lavacharts\Javascript
 * @since     4.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @license   http://opensource.org/licenses/MIT      MIT
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Documentation
 */
class NodeModule
{
    /**
     * node_modules root
     *
     * @var string
     */
    const ROOT = __DIR__ . '/../../node_modules';

    /**
     * Opening javascript tag.
     *
     * @var string
     */
    const JS_OPEN = '<script type="text/javascript">';

    /**
     * Closing javascript tag.
     *
     * @var string
     */
    const JS_CLOSE = '</script>';

    /**
     * @var string
     */
    public $pkgName;

    /**
     * @var false|string
     */
    private $moduleRoot;

    /**
     * Build a new NodeModule that can find files from its root.
     *
     * @param string $pkgName
     */
    public function __construct(string $pkgName)
    {
        $this->pkgName = $pkgName;
        $this->moduleRoot = realpath(self::ROOT) . '/' . $this->pkgName;
    }

    /**
     * Resolve a file from the package
     *
     * @param string $file Filename
     * @return string
     */
    public function resolve(string $file): string
    {
        return $this->moduleRoot . '/dist/' . $file;
    }

    /**
     * Get the contents of the script as a <script> to input to the page
     *
     * @param string $file
     * @return Buffer
     */
    public function getScriptTag(string $file): Buffer
    {
        $buffer = new Buffer($this->getFileContents($file));

        $buffer->prepend(self::JS_OPEN)->append(self::JS_CLOSE);

         return $buffer;
    }

    /**
     * Get the contents of the script as a string
     *
     * @param string $file
     * @return string
     */
    public function getFileContents(string $file): string
    {
        return file_get_contents($this->resolve($file));
    }
}
