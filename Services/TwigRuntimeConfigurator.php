<?php

namespace Prokl\TwigExtensionsPackBundle\Services;

use Twig\Environment;

/**
 * Class TwigRuntimeConfigurator
 * @package Prokl\TwigExtensionsPackBundle\Services
 *
 * @since 10.08.2021
 */
class TwigRuntimeConfigurator
{
    /**
     * @var array $handlers Runtimers (помечены тэгом twig.runtime).
     */
    private $handlers = [];

    /**
     * @var Environment $twig Twig.
     */
    private $twig;

    /**
     * TwigRuntimeConfigurator constructor.
     *
     * @param Environment $twig         Twig.
     * @param mixed       ...$runtimers Runtimers.
     */
    public function __construct(Environment $twig, ...$runtimers)
    {
        $this->twig = $twig;

        foreach ($runtimers as $runtimer) {
            $iterator = $runtimer->getIterator();
            $this->handlers[] = iterator_to_array($iterator);
        }

        if (array_key_exists(0, $this->handlers)) {
            $this->handlers = $this->handlers[0];
        }
    }

    /**
     * Регистрация runtimers.
     *
     * @return void
     */
    public function register() : void
    {
        foreach ($this->handlers as $handler) {
            $this->twig->addRuntimeLoader($handler);
        }
    }
}