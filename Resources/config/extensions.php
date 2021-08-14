<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Bridge\Twig\DataCollector\TwigDataCollector;
use Symfony\Bridge\Twig\Extension\CodeExtension;
use Symfony\Bridge\Twig\Extension\ProfilerExtension;
use Symfony\Bridge\Twig\Extension\StopwatchExtension;
use Symfony\Bridge\Twig\Extension\WebLinkExtension;
use Twig\Profiler\Profile;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('twig.extension.code', CodeExtension::class)
        ->public()
        ->args([service('debug.file_link_formatter')->ignoreOnInvalid(), param('kernel.project_dir'), param('kernel.charset')])
        ->tag('twig.extension')

        ->set('twig.extension.profiler', ProfilerExtension::class)
        ->public()
        ->args([service('twig.profile'), service('debug.stopwatch')->ignoreOnInvalid()])

        ->set('twig.extension.debug.stopwatch', StopwatchExtension::class)
        ->public()
        ->args([service('debug.stopwatch')->ignoreOnInvalid(), param('kernel.debug')])

        ->set('twig.extension.profiler', ProfilerExtension::class)
        ->public()
        ->args([service('twig.profile'), service('debug.stopwatch')->ignoreOnInvalid()])

        ->set('twig.profile', Profile::class)->public()

        ->set('twig.extension.weblink', WebLinkExtension::class)
        ->args([service('request_stack')])->public()
        ->tag('twig.extension')

        ->set('data_collector.twig', TwigDataCollector::class)
        ->public()
        ->args([service('twig.profile'), service('twig')])
        ->tag('data_collector', ['template' => '@WebProfiler/Collector/twig.html.twig', 'id' => 'twig', 'priority' => 257])
    ;
};
