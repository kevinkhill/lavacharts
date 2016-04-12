<?php

namespace Khill\Lavacharts\Javascript;

use Khill\Lavacharts\Support\Buffer;

/**
 * JavascriptFactory Class
 *
 * This class takes charts and uses all the info to build the complete
 * javascript blocks for outputting into the page.
 *
 * @category   Class
 * @package    Khill\Lavacharts\Javascript
 * @since      2.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2016, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class JavascriptFactory
{
    /**
     * Javascript output buffer.
     *
     * @var \Khill\Lavacharts\Support\Buffer
     */
    protected $buffer;

    /**
     * Javascript template for output.
     *
     * @var string
     */
    protected $template;

    /**
     * Array of variables and values to apply to the javascript template.
     *
     * @var array
     */
    protected $templateVars;

    /**
     * JavascriptFactory constructor.
     *
     * @param string $outputTemplate
     */
    public function __construct($outputTemplate)
    {
        $this->template = file_get_contents(
            realpath(__DIR__ . $outputTemplate)
        );

        $this->buffer = new Buffer($this->template);

        foreach ($this->templateVars as $var => $value) {
            $this->buffer->replace('<'.$var.'>', $value);
        }
    }

    /**
     * Returns the output buffer for the javascript.
     *
     * @return \Khill\Lavacharts\Support\Buffer
     */
    public function getOutputBuffer()
    {
        return $this->buffer;
    }
}
