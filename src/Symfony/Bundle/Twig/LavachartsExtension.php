<?php

namespace Khill\Lavacharts\Symfony\Bundle\Twig;

use Khill\Lavacharts\Lavacharts;

class LavachartsExtension extends \Twig_Extension
{
    /**
     * The Lavacharts object passed in from the service container.
     *
     * @var \Khill\Lavacharts\Lavacharts
     */
    private $lava;

    public function __construct($lava)
    {
        $this->lava = $lava;
    }

    public function getFunctions()
    {
        $chartClassesProp = new \ReflectionProperty(Lavacharts::class, 'chartClasses');
        $chartClassesProp->setAccessible(true);
        $chartClasses = $chartClassesProp->getValue(new Lavacharts);

        $renderFunctions = [];

        foreach ($chartClasses as $chartClass) {
            $renderFunctions[] = new \Twig_SimpleFunction(strtolower($chartClass),
                function($chartLabel, $elemId) use ($chartClass) {
                    return $this->renderChart($chartClass, $chartLabel, $elemId);
                }
            );
        }

        return $renderFunctions;
    }

    public function renderChart($chartType, $chartLabel, $elemId)
    {
        return $this->lava->render($chartType, $chartLabel, $elemId);
    }

    public function getName()
    {
        return 'lavacharts_twig_extension';
    }
}
