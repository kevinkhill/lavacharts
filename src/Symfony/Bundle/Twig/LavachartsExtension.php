<?php

namespace Khill\Lavacharts\Symfony\Bundle\Twig;

use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Charts\ChartFactory;

class LavachartsExtension extends \Twig_Extension
{
    /**
     * The Lavacharts object passed in from the service container.
     *
     * @var \Khill\Lavacharts\Lavacharts
     */
    private $lava;

    /**
     * LavachartsExtension constructor.
     *
     * @param \Khill\Lavacharts\Lavacharts $lava
     */
    public function __construct(Lavacharts $lava)
    {
        $this->lava = $lava;
    }

    /**
     * Add Twig functions to utilize Lavacharts from the container.
     *
     * To render, just use the lowercase name of the chart:
     * and pass through the raw filter.
     *
     * Example:
     *  {{ linechart('Stocks")|raw }}
     *
     *
     * @return array Array of twig functions
     */
    public function getFunctions()
    {
        $renderables = array_merge(['dashboard'], ChartFactory::$CHART_TYPES);

        $renderFunctions = [];

        foreach ($renderables as $renderable) {
            $renderFunctions[] = new \Twig_SimpleFunction(strtolower($renderable),
                function($label) use ($renderable) {
                    try {
                        $elementId = func_get_arg(1);

                        return $this->lava->render($renderable, $label, $elementId);
                    } catch (\Exception $e) {
                        return $this->lava->render($renderable, $label);
                    }
                }
            );
        }

        return $renderFunctions;
    }

    /**
     * Returns the name of the Lavacharts Twig Extension
     *
     * @return string
     */
    public function getName()
    {
        return 'lavacharts_twig_extension';
    }
}
