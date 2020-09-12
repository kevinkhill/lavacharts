<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Support\Buffer;

/**
 * ScriptBuffer Class
 *
 * Used for building <script> tags
 *
 * @package   Khill\Lavacharts\Support
 * @since     4.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2015, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT MIT
 */
class ScriptBuffer extends Buffer
{
    /**
     * Buffer constructor.
     *
     * @param string|mixed $str
     */
    public function __construct($str)
    {
        parent::__construct($str);

        $this->prepend(ScriptManager::JS_OPEN)
             ->append(ScriptManager::JS_CLOSE);
    }
}
