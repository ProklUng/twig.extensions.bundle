<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ControllerExtension
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions
 *
 * @since 20.10.2020
 * @since 24.10.2020 Обновление интерфейса. Убран deprecated Twig_ExtensionInterface. Причесывание.
 * @since 03.11.2020 Чистка.
 */
class ControllerExtension extends AbstractExtension
{
    /**
     * Return extension name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'controller_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('controller', [$this, 'getControllerReference']),
        ];
    }

    /**
     * Controller reference.
     *
     * @param string $controller Контроллер.
     * @param array  $atrributes Аттрибуты.
     * @param array  $query      $_GET.
     *
     * @return ControllerReference
     */
    public function getControllerReference(
        string $controller,
        array $atrributes = [],
        array $query = []
    ) : ControllerReference {
            return new ControllerReference($controller, $atrributes, $query);
    }
}
