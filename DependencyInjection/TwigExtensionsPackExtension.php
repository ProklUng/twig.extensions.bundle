<?php

namespace Prokl\TwigExtensionsPackBundle\DependencyInjection;

use Exception;
use Prokl\WpSymfonyRouterBundle\Services\Utils\DispatchController;
use Mobile_Detect;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class TwigExtensionsPackExtension
 * @package Prokl\TwigExtensionsPack\DependencyInjection
 *
 * @since 22.04.2021
 */
class TwigExtensionsPackExtension extends Extension
{
    private const DIR_CONFIG = '/../Resources/config';

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container) : void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . self::DIR_CONFIG)
        );

        $loader->load('services.yaml');

        // Определяю Wordpress
        if (defined('ABSPATH')) {
            $loader->load('wordpress.yaml');
        }

        // Определяю Битрикс.
        if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) {
            $loader->load('bitrix.yaml');
        }

        // Если не установлен Symfony Router Bundle, то удаляю расширение render.
        if (!class_exists(DispatchController::class)) {
            $container->removeDefinition('twig_extension_bundle.render');
            $container->removeDefinition('twig_extension_bundle.twig.paths');
        }

        if (!class_exists(Mobile_Detect::class)) {
            $container->removeDefinition('twig_extension_bundle.mobile.detect.extension');
        }
    }

    /**
     * @inheritDoc
     */
    public function getAlias() : string
    {
        return 'twig_extension_spack';
    }
}
