<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Functions;

use Prokl\WpSymfonyRouterBundle\Services\Utils\RouteChecker;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class SymfonyTwigPath
 * Функция path Твиг из Symfony.
 * @package Prokl\TwigExtensionsPackBundle\Twig\Functions
 *
 * @since 23.09.2020 Выпиливание League Container.
 * @since 14.10.2020 Выпиливание auto-wiring.
 * @since 01.12.2020 Внедрение сервиса routing.utils.
 */
class SymfonyTwigPath extends AbstractExtension
{
    /**
     * @var RouteChecker $routingService Утилиты по работе с роутингом.
     */
    private $routingService;

    /**
     * @var ParameterBag $parameterBag Параметры контейнера.
     */
    private $parameterBag;

    /**
     * SymfonyTwigPath constructor.
     *
     * @param RouteChecker      $routing      Утилиты по работе с роутингом.
     * @param ParameterBag $parameterBag Параметры контейнера.
     */
    public function __construct(
        RouteChecker $routing,
        ParameterBag $parameterBag
    ) {
        $this->routingService = $routing;
        $this->parameterBag = $parameterBag;
    }

    /**
     * Return extension name
     *
     * @return string
     */
    public function getName()
    {
        return 'twig_path';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('path', [$this, 'path']),
            new TwigFunction('url', [$this, 'url']),
            new TwigFunction('absolute_url', [$this, 'absolute_url']),
        ];
    }

    /**
     * Путь по роуту и его параметрам.
     *
     * @param string $route            ID роута.
     * @param array  $route_parameters Параметры.
     *
     * @return string
     */
    public function path(string $route, array $route_parameters = []) : string
    {
        return $this->routingService->generateUrl($route, $route_parameters);
    }

    /**
     * Абсолютный (со схемой и хостом) путь.
     *
     * @param string $relativePath Относительный путь.
     *
     * @return string
     */
    public function absoluteUrl(string $relativePath) : string
    {
        if (!$relativePath) {
            return '';
        }

        $schema = (string)$this->parameterBag->get('kernel.schema');
        $host = (string)$this->parameterBag->get('kernel.http.host');

        return $schema . '://' . $host . $relativePath;
    }

    /**
     * Абсолютный (со схемой и хостом) путь по роуту и его параметрам.
     *
     * @param string $route            ID роута.
     * @param array  $route_parameters Параметры.
     *
     * @return string
     */
    public function url(string $route, array $route_parameters = []) : string
    {
        return $this->routingService->generateUrl($route, $route_parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
