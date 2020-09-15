<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Support\Buffer;

/**
 * Class ScriptTag
 *
 * Uses for building string outputs to send to the browser
 *
 * @package   Khill\Lavacharts\Javascript
 * @since     4.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2015, KHill Designs
 * @license   http://opensource.org/licenses/MIT      MIT
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 */
class ScriptTag extends Buffer
{
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
     * ScriptTag constructor.
     *
     * @param string|Buffer $contents
     */
    public function __construct($contents = '')
    {
        parent::__construct($contents);
    }

    /**
     * Returns the contents of the buffer when accessed as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->prepend(self::JS_OPEN)->append(self::JS_CLOSE);
    }
}
