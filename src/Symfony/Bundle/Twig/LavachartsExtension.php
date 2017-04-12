<?php

namespace Khill\Lavacharts\Symfony\Bundle\Twig;

use Khill\Lavacharts\Lavacharts;
use Khill\Lavacharts\Charts\ChartFactory;

class LavachartsExtension extends \Twig_Extension
{
    /**
     * Twig Extension Name
     *
     * @var string
     */
    const NAME = 'lavacharts_twig_extension';

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
     * Add Twig functions as short-cut methods to the rendering methods.
     *
     * @return array
     */
    public function getFunctions()
    {
        $renderables = array_merge(['dashboard'], ChartFactory::$CHART_TYPES);

        $renderFunctions = [];

        foreach ($renderables as $renderable) {
            $renderFunctions[] = new \Twig_SimpleFunction(strtolower($renderable),
                function($label, $elementId) use ($renderable) {
                    return $this->renderChart($renderable, $label, $elementId);
                }
            );
        }

        return $renderFunctions;
    }

    /**
     * @param string $type
     * @param string $label
     * @param string $elementId
     * @return string
     */
    public function renderChart($type, $label, $elementId)
    {
        return $this->lava->render($type, $label, $elementId);
    }

    /**
     * Returns the name of the Lavacharts Twig Extension
     *
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
