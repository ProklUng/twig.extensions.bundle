<?php

namespace Prokl\TwigExtensionsPackBundle\Services;

use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use Timber\Timber;

/**
 * Class TwigConfiguratorWordpress
 * @package Prokl\TwigExtensionsPackBundle\Services
 *
 * @since 10.05.2021
 */
class TwigConfiguratorWordpress
{
    /**
     * @var ContainerBag $containerBag Параметры из контейнера.
     */
    private $containerBag;

    /**
     * @var Timber $twig Твиг.
     */
    private $twig;

    /**
     * @var array $configuration Конфигурация TWIG.
     */
    private $configuration = [];

    /**
     * TwigConfiguratorWordpress constructor.
     *
     * @param ContainerBag $containerBag
     * @param Timber       $twig
     */
    public function __construct(ContainerBag $containerBag, Timber $twig)
    {
        $this->containerBag = $containerBag;
        $this->twig = $twig;

        if ($this->containerBag->has('twig')) {
            $this->configuration = $this->containerBag->get('twig');
        }

        $this->configuration['web_paths'] = (array)$this->configuration['paths'];
        $this->configuration['paths'] = $this->checkTwigTemplatesPath((array)$this->configuration['paths']);

        // Кэширование (если нужно).
        $this->caching();
    }

    /**
     * Настройка кэширования.
     *
     * @return void
     *
     * @since 29.11.2020
     */
    public function caching() : void
    {
        // Директория, где будет лежать кэш.
        if (array_key_exists('cache_dir', $this->configuration) && $this->configuration['cache_dir']) {
            add_filter('timber/cache/location', function () : string {
                return (string)$this->configuration['cache.dir'];
            });
        }

        if (array_key_exists('cache', $this->configuration)
            && $this->configuration['cache']) {
            $this->twig::${'cache'} = true;
        }
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
     * Серверные пути к TWIG шаблонам.
     *
     * @return array
     *
     * @since 04.02.2021
     */
    public function pathTemplates() : array
    {
        $paths = $this->getTwigTemplatesPath();

        if (!$paths) {
            throw new RuntimeException(
                'Не сконфигурированы настройки путей к шаблонам Twig.'
            );
        }

        return $paths;
    }

    /**
     * Locations Твига.
     *
     * @return array
     */
    public function locations() : array
    {
        return $this->twig::${'locations'};
    }

    /**
     * Установить массив locations целиком.
     *
     * @param array $value
     *
     * @return array
     */
    public function setLocation(array $value) : array
    {
        $this->twig::${'locations'} = $value;

        return $this->twig::${'locations'};
    }

    /**
     * Добавить путь в Timber::$locations.
     *
     * @param string $append
     *
     * @return array
     */
    public function appendLocation(string $append) : array
    {
        $this->twig::${'locations'}[] = $append;

        return array_unique($this->twig::${'locations'});
    }

    /**
     * Проверить существование путей.
     *
     * @param array $paths Пути из конфига.
     *
     * @return array
     *
     * @since 26.01.2021
     */
    private function checkTwigTemplatesPath(array $paths) : array
    {
        $arResult = [];

        if (!$paths) {
            return [];
        }

        foreach ($paths as $path) {
            if (@is_dir(ABSPATH . $path)) {
                $arResult[] = ABSPATH . $path;
                continue;
            }

            throw new RuntimeException(
                'Не найден путь к шаблону Twig: '.$path
            );
        }

        return $arResult;
    }
}