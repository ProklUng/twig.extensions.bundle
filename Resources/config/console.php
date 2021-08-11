<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Prokl\TwigExtensionsPackBundle\Command\DebugCommand;
use Prokl\TwigExtensionsPackBundle\Command\LintCommand;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('twig.command.debug', DebugCommand::class)
            ->args([
                service('twig'),
                param('kernel.project_dir'),
                param('kernel.bundles_metadata'),
                param('twig_default_path'),
                service('debug.file_link_formatter')->nullOnInvalid(),
            ])
            ->tag('console.command', ['command' => 'debug:twig'])

        ->set('twig.command.lint', LintCommand::class)
            ->args([service('twig')])
            ->tag('console.command', ['command' => 'lint:twig'])
    ;
};
