<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ContainerTwigExtension
 * Container extension.
 * @package Local\Services\Twig\Extensions
 *
 * @since 11.10.2020
 * @since 24.10.2020 Обновление интерфейса. Убран deprecated Twig_ExtensionInterface.
 * @since 03.11.2020 Чистка.
 */
class ContainerTwigExtension extends AbstractExtension
{
    use ContainerAwareTrait;

    /**
     * Return extension name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'twig/container';
    }

    /**
     * Функции.
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('service', [$this, 'service']),
            new TwigFunction('param', [$this, 'param']),
        ];
    }

    /**
     * Вызов сервиса.
     *
     * @param string $service Сервис.
     *
     * @return object|null
     */
    public function service(string $service) : ?object
    {
        return $this->container->get($service);
    }

    /**
     * Параметр контейнера.
     *
     * @param string $param Переменная.
     *
     * @return mixed
     */
    public function param(string $param)
    {
        return $this->container->getParameter($param);
    }
}
