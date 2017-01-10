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
 * @copyright  (c) 2017, KHill Designs
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
            realpath(__DIR__ . '/../../javascript/templates/' . $outputTemplate)
        );

        $this->buffer = new Buffer($this->template);

        /** Replacing the template variables with values */
        foreach ($this->getTemplateVars() as $var => $value) {
            $this->buffer->replace('<'.$var.'>', $value);
        }

        /** Converting string dates to date constructors */
        $this->buffer->pregReplace('/"Date\(((:?[0-9]+,?)+)\)"/', 'new Date(\1)');

        /** Converting string nulls to actual nulls */
        $this->buffer->pregReplace('/"null"/', 'null');
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
