<?php

namespace Prokl\TwigExtensionsPackBundle\DependencyInjection\CompilerPass;

use RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class TwigExtensionConfigurator
 * @package Prokl\TwigExtensionsPackBundle\DependencyInjection\CompilerPass
 *
 * @since 08.08.2021
 */
class TwigExtensionConfigurator implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('session.instance') || !$container->hasDefinition('global.request')) {
            $this->removeIfExists($container, 'app.twig.variable');
            $this->removeIfExists($container, 'Prokl\TwigExtensionsPackBundle\Services\Handlers\AppVariable');

            $this->removeIfExists($container, 'twig.variables');
            $this->removeIfExists($container, 'Prokl\TwigExtensionsPackBundle\Services\ConfigureVariables');
        }

        // Кэшер для runtime директивы cacher.
        $cacherTwigDefinition = $container->getDefinition('twig.cache.runtime');
        $serviceCacherId = $container->getParameter('twig.cacher');

        // null => значит фича отключена.
        if ($serviceCacherId === null) {
            $container->removeDefinition('twig.cache.runtime');
            return;
        }

        if (!$container->hasDefinition($serviceCacherId)) {
            throw new RuntimeException('Invalid service in cache config key:  ' . $serviceCacherId);
        }

        $newCacherDef = $container->getDefinition($serviceCacherId);
        $cacherTwigDefinition->replaceArgument(0, $newCacherDef);
    }

    /**
     * Удалить (сервис, алиас), если существует.
     *
     * @param ContainerBuilder $container Контейнер.
     * @param string           $serviceId ID сервиса.
     *
     * @return void
     */
    private function removeIfExists(ContainerBuilder $container, string $serviceId)
    {
        if ($container->hasDefinition($serviceId)) {
            $container->removeDefinition($serviceId);
        }

        if ($container->hasAlias($serviceId)) {
            $container->removeAlias($serviceId);
        }
    }
}
