<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class RouteExtension
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions
 *
 * @since 22.10.2020
 */
class RouteExtension extends AbstractExtension
{
    /**
     * @var RouteCollection $routeCollection Коллекция роутов.
     */
    private $routeCollection;

    /**
     * @var ParameterBag $parameterBag Параметры контейнера.
     */
    private $parameterBag;

    /**
     * RouteExtension constructor.
     *
     * @param RouteCollection $routeCollection Route collection.
     * @param ParameterBag    $parameterBag    Параметры.
     */
    public function __construct(
        RouteCollection $routeCollection,
        ParameterBag $parameterBag
    ) {
        $this->routeCollection = $routeCollection;
        $this->parameterBag = $parameterBag;
    }


    /**
     * Return extension name
     *
     * @return string
     */
    public function getName()
    {
        return 'route_url_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('url', [$this, 'url']),
            new TwigFunction('absolute_url', [$this, 'absoluteUrl']),
            new TwigFunction('path', [$this, 'path']),
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
        $routeParams = $this->routeCollection->get($route);

        if (!$routeParams) {
            return '';
        }

        $urlGenerator = new UrlGenerator(
            $this->routeCollection,
            new RequestContext()
        );

        try {
            return $urlGenerator->generate($route, $route_parameters);
        } catch (Exception $e) {
            return '';
        }
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

        return $schema . $host . $relativePath;
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
        $relativePath = $this->path($route, $route_parameters);
        if (!$relativePath) {
            return '';
        }

        return $this->absoluteUrl($relativePath);
    }
}
