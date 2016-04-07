<?php

namespace Khill\Lavacharts\Javascript;

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
     * Directory to javascript sources.
     *
     * @var string
     */
    const JS_DIR = '/../../javascript/';

    /**
     * Javascript output buffer.
     *
     * @var string
     */
    protected $buffer;

    /**
     * Javascript template for output.
     *
     * @var string
     */
    protected $template;

    /**
     * Map of template vars to values.
     *
     * @var array
     */
    protected $templateVars;

    /**
     * Create a new JavascriptFactory based off of an output template.
     *
     * @param string $outputTemplate Location of the js output template.
     */
    public function __construct($outputTemplate)
    {
        $templateDir = realpath(__DIR__ . self::JS_DIR . $outputTemplate);

        $this->template     = file_get_contents($templateDir);
        $this->templateVars = $this->getTemplateVars();
    }

    /**
     * Parses the javascript template and wraps the output in a script tag.
     *
     * @return string Javascript code block.
     */
    public function getJavascript()
    {
        $this->parseTemplate();

        return ScriptManager::scriptTagWrap($this->buffer);
    }

    /**
     * Parses javascript templates with the value mappings
     *
     * @return string Javascript
     */
    protected function parseTemplate()
    {
        $this->buffer = $this->template;

        foreach ($this->templateVars as $key => $value) {
            $this->buffer = preg_replace("/<$key>/", $value, $this->buffer);
        }
    }
}
