<?php

namespace Prokl\TwigExtensionsPackBundle\DependencyInjection;

use Exception;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class TwigConfig
 * Твиг.
 * @package Fedy\App
 *
 * @since 10.05.2021
 */
class TwigConfig
{
    /**
     * @var Container $containerBag Параметры из контейнера.
     */
    private $container;

    /**
     * @var array $configuration Конфигурация TWIG.
     */
    private $configuration;

    /**
     * TwigConfig constructor.
     *
     * @param Container $container Параметры из контейнера.
     * @param array     $configuration
     */
    public function __construct(
        Container $container,
        array $configuration = []
    ) {
        $this->container = $container;
        $this->configuration = $configuration;
    }

    /**
     * Серверные пути к твиговским шаблонам.
     *
     * @return array
     *
     * @since 26.01.2021 Упрощение.
     */
    public function getTwigTemplatesPath(): array
    {
        return (array)$this->configuration['paths'];
    }

    /**
     * Секция Parameters.
     *
     * @return array
     * @throws Exception
     */
    public function processGlobals(): array
    {
        if (count($this->configuration['globals']) === 0) {
            return [];
        }

        $result = [];

        foreach ($this->configuration['globals'] as $varKey => $parameterValue) {
            if (!$varKey) {
                continue;
            }

            // Сервис или массив в глобальных переменных.
            if (is_object($parameterValue) || is_array($parameterValue)) {
                $result[$varKey] = $parameterValue;
                continue;
            }

            if (is_string($parameterValue) && $this->container->has($parameterValue)) {
                $result[$varKey] = $this->container->get($parameterValue);
                continue;
            }

            // Переменные из сервис-провайдера.
            $result[$varKey] = $this->tryResolveVariable((string)$parameterValue);
        }

        return $result;
    }

    /**
     * Обработка переменных из сервис-провайдера.
     *
     * @param string $parameterValue
     *
     * @return mixed|string
     * @throws Exception Когда не найдена переменная в контейнере.
     */
    private function tryResolveVariable(string $parameterValue)
    {
        if (strpos($parameterValue, '%') !== false) {
            $variable = str_replace('%', '', $parameterValue);
            if (!$this->container->has($variable)) {
                throw new \RuntimeException(
                    'Нет такой переменной в сервис-провайдере . '.$variable
                );
            }

            return $this->container->get($variable);
        }

        return $parameterValue;
    }
}
