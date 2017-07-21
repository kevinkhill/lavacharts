<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Support\Buffer;

/**
 * Class Buffer
 *
 * Uses for building string outputs to send to the browser
 *
 * @package   Khill\Lavacharts\Support
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2015, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT MIT
 */
class ScriptBuffer extends Buffer
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
     * Buffer constructor.
     *
     * @param string|mixed $str
     */
    public function __construct($str)
    {
        parent::__construct($str);

        $this->prepend(static::JS_OPEN)->append(static::JS_CLOSE);
    }
}
