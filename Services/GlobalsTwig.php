<?php

namespace Prokl\TwigExtensionsPackBundle\Services;

use Exception;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Twig\Environment;

/**
 * Class GlobalsTwig
 * @package Prokl\TwigExtensionsPackBundle\Services
 */
class GlobalsTwig
{
    use ContainerAwareTrait;

    /**
     * @var array $globals Секция globals из конфига.
     */
    private $globals;

    /**
     * GlobalsTwig constructor.
     *
     * @param array $globals Секция globals из конфига.
     */
    public function __construct(array $globals)
    {
        $this->globals = $globals;
    }

    /**
     * Инициализация событий Wordpress.
     *
     * @return void
     */
    public function hooksInit(): void
    {
        // Globals
        add_filter('timber/twig', [$this, 'addGlobals']);

        // Модификация глобального контекста Twig.
        add_filter('timber_context', [$this, 'initTimberVariables']);
    }

    /**
     * Globals.
     *
     * @param Environment $twig Twig.
     *
     * @return Environment
     * @throws Exception
     */
    public function addGlobals(Environment $twig): Environment
    {
        if ($this->container->hasParameter('twig.globals')) {
            foreach ((array)$this->container->getParameter('twig.globals') as $name => $global) {
                if (is_string($name)) {
                    $twig->addGlobal($name, $global);
                    continue;
                }

                throw new Exception(
                    'Global Twig variable name must be string! Got ' . gettype($name)
                );
            }
        }

        return $twig;
    }

    /**
     * @param array $context
     *
     * @return array
     * @throws Exception
     */
    public function initTimberVariables(array $context): array
    {
        return array_merge($context, $this->globals);
    }
}