<?php

namespace Prokl\TwigExtensionsPackBundle\DependencyInjection;

use Exception;
use Prokl\TwigExtensionsPackBundle\Services\TwigExtensionsBag;
use Prokl\TwigExtensionsPackBundle\Services\TwigRuntimesBag;
use Prokl\TwigExtensionsPackBundle\Twig\Extensions\RouteExtension;
use Prokl\WpSymfonyRouterBundle\Services\Utils\DispatchController;
use Mobile_Detect;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Prokl\TwigExtensionsPackBundle\Twig\Functions\SymfonyEncore;
use Prokl\BitrixSymfonyRouterBundle\Services\Utils\RouteChecker;
use Symfony\Component\Stopwatch\Stopwatch;

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
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('twig.cacher', $config['cacher']);
        $container->setParameter('twig.runtimes_export', $config['runtimes_export']);
        $container->setParameter('twig_extension_bundle.build_dev_path', $config['webpack_build_dev_path']);
        $container->setParameter('twig_extension_bundle.build_production_path', $config['webpack_build_production_path']);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . self::DIR_CONFIG)
        );

        $loaderPhp = new PhpFileLoader(
            $container,
            new FileLocator(__DIR__ . self::DIR_CONFIG)
        );

        $loader->load('services.yaml');

        if (class_exists(Application::class)) {
            $loaderPhp->load('console.php');
        }

        if (!class_exists(Stopwatch::class)) {
            $container->removeDefinition('twig.extension.profiler');
            $container->removeDefinition('twig.extension.debug.stopwatch');
        }

        $loaderPhp->load('extensions.php');

        // ?????????????????? Wordpress
        if (defined('ABSPATH')) {
            $loader->load('wordpress.yaml');

            // ???????? ???? ???????????????????? Symfony Router Bundle, ???? ???????????? ???????????????????? render.
            if (!class_exists(DispatchController::class)) {
                $container->removeDefinition('twig_extension_bundle.render');
                $container->removeDefinition('twig_extension_bundle.twig.paths');
                $container->removeDefinition(RouteExtension::class);
            }
        }

        // ?????????????????? ??????????????.
        if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) {
            $loader->load('bitrix.yaml');

            if (!class_exists(RouteChecker::class)) {
                $container->removeDefinition('Prokl\TwigExtensionsPackBundle\Twig\Functions\Bitrix\SymfonyTwigPath');
                $container->removeDefinition(RouteExtension::class);
            }

            // ???? ???????????????????? tools.twig - ?????????????? ????????????.
            if (!class_exists(TemplateEngine::class)) {
                $container->removeDefinition(TwigExtensionsBag::class);
                $container->removeDefinition(TwigRuntimesBag::class);
            }
        }

        if (!class_exists(Mobile_Detect::class)) {
            $container->removeDefinition('twig_extension_bundle.mobile.detect.extension');
        }

        if (!$container->hasDefinition('assets.manager')) {
            $container->removeDefinition(SymfonyEncore::class);
        }
    }

    /**
     * @inheritDoc
     */
    public function getAlias() : string
    {
        return 'twig_extension_pack';
    }
}