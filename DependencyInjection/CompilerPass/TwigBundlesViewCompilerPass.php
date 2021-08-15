<?php

namespace Prokl\TwigExtensionsPackBundle\DependencyInjection\CompilerPass;

use Symfony\Component\Config\Resource\FileExistenceResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class TwigBundlesViewCompilerPass
 * @package Prokl\TwigExtensionsPackBundle\DependencyInjection\CompilerPass
 *
 * @since 12.08.2021
 */
final class TwigBundlesViewCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('twig.loader')) {
            return;
        }

        $twigFilesystemLoaderDefinition = $container->getDefinition('twig.loader');

        $bundleHierarchy = $this->getBundleTemplatePaths($container, [
            'default_path' => ''
        ]);

        foreach ($bundleHierarchy as $name => $paths) {
            $namespace = $this->normalizeBundleName($name);
            foreach ($paths as $path) {
                $twigFilesystemLoaderDefinition->addMethodCall('addPath', [$path, $namespace]);
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     *
     * @return array
     */
    private function getBundleTemplatePaths(ContainerBuilder $container, array $config): array
    {
        $bundleHierarchy = [];
        foreach ($container->getParameter('kernel.bundles_metadata') as $name => $bundle) {
            $defaultOverrideBundlePath = $container->getParameterBag()->resolveValue($config['default_path']).'/bundles/'.$name;

            if (file_exists($defaultOverrideBundlePath)) {
                $bundleHierarchy[$name][] = $defaultOverrideBundlePath;
            }
            $container->addResource(new FileExistenceResource($defaultOverrideBundlePath));

            if (file_exists($dir = $bundle['path'].'/Resources/views') || file_exists($dir = $bundle['path'].'/templates')) {
                $bundleHierarchy[$name][] = $dir;
            }

            $container->addResource(new FileExistenceResource($dir));
        }

        return $bundleHierarchy;
    }

    /**
     * @param string $name Класс бандла.
     *
     * @return string
     */
    private function normalizeBundleName(string $name): string
    {
        if (str_ends_with($name, 'Bundle')) {
            $name = substr($name, 0, -6);
        }

        return $name;
    }
}