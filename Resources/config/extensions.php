<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Bridge\Twig\DataCollector\TwigDataCollector;
use Symfony\Bridge\Twig\Extension\CodeExtension;
use Symfony\Bridge\Twig\Extension\ProfilerExtension;
use Symfony\Bridge\Twig\Extension\StopwatchExtension;
use Twig\Profiler\Profile;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('twig.extension.code', CodeExtension::class)
        ->args([service('debug.file_link_formatter')->ignoreOnInvalid(), param('kernel.project_dir'), param('kernel.charset')])
        ->tag('twig.extension')

        ->set('twig.extension.profiler', ProfilerExtension::class)
        ->args([service('twig.profile'), service('debug.stopwatch')->ignoreOnInvalid()])

        ->set('twig.extension.debug.stopwatch', StopwatchExtension::class)
        ->args([service('debug.stopwatch')->ignoreOnInvalid(), param('kernel.debug')])

        ->set('twig.extension.profiler', ProfilerExtension::class)
        ->args([service('twig.profile'), service('debug.stopwatch')->ignoreOnInvalid()])

        ->set('twig.profile', Profile::class)

        ->set('data_collector.twig', TwigDataCollector::class)
        ->args([service('twig.profile'), service('twig')])
        ->tag('data_collector', ['template' => '@WebProfiler/Collector/twig.html.twig', 'id' => 'twig', 'priority' => 257])
    ;
};
