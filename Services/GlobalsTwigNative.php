<?php

namespace Prokl\TwigExtensionsPackBundle\Services;

use Exception;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Twig\Environment;

/**
 * Class GlobalsTwigNative
 * @package Prokl\TwigExtensionsPackBundle\Services
 */
class GlobalsTwigNative
{
    use ContainerAwareTrait;

    /**
     * @var array $globals Секция globals из конфига.
     */
    private $globals;

    /**
     * @var Environment $twig Инстанц Твига.
     */
    private $twig;

    /**
     * GlobalsTwigNative constructor.
     *
     * @param Environment $twig     Инстанц Твига.
     * @param array       $globals  Секция globals из конфига.
     */
    public function __construct(Environment $twig, array $globals)
    {
        $this->twig = $twig;
        $this->globals = $globals;
    }

    /**
     * Globals.
     *
     * @return void
     * @throws Exception
     */
    public function addGlobals(): void
    {
        if ($this->container->hasParameter('twig.globals')) {
            foreach ($this->container->getParameter('twig.globals') as $name => $global) {
                $this->twig->addGlobal($name, $global);
            }
        }
    }
}