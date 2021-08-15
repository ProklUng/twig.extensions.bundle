<?php

namespace Prokl\TwigExtensionsPackBundle\DependencyInjection\CompilerPass;

use Prokl\TwigExtensionsPackBundle\Services\TwigRuntimesBag;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class TwigTunerCompilerPass
 * @package Prokl\BitrixOrdinaryToolsBundle\DependencyInjection\CompilerPass
 *
 * @since 11.08.2021
 */
final class TwigTunerCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('twig.runtimes_export')
           &&
           !$container->getParameter('twig.runtimes_export')
        ) {
            $container->removeDefinition(TwigRuntimesBag::class);
        }
    }
}
