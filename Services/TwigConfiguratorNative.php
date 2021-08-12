<?php

namespace Prokl\TwigExtensionsPackBundle\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;

/**
 * Class TwigConfiguratorNative
 * @package Prokl\TwigExtensionsPackBundle\Services
 */
class TwigConfiguratorNative
{
    /**
     * @var ContainerBag $containerBag Параметры.
     */
    private $containerBag;

    /**
     * @var array $config Конфигурация.
     */
    private $config = [];

    /**
     * TwigConfiguratorNative constructor.
     *
     * @param ContainerBag $containerBag Параметры.
     */
    public function __construct(ContainerBag $containerBag)
    {
        $this->containerBag = $containerBag;
        if ($this->containerBag->has('twig_config')) {
            $this->config = (array)$this->containerBag->get('twig_config');
        }
    }

    /**
     * @return array
     */
    public function paths() : array
    {
        if (!$this->containerBag->has('twig_paths')) {
            return [];
        }

        return (array)$this->containerBag->get('twig_paths');
    }

    /**
     * @return array
     */
    public function config() : array
    {
        return $this->config;
    }
}