<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ApplyFilterExtension
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions
 *
 * @since 17.01.2021
 *
 * @example
 *
 * {{ apply_filter('max', {2: "e", 1: "a", 3: "b", 5: "d", 4: "c"}) }}
 * {{ apply_filter('title', 'my first car') }}", 'My First Car']
 * {{ apply_filter('first','1234') }}", '1' }}
 * {{ apply_filter('date','01-01-2020') }}", 'January 1, 2020 00:00'] }}
 * {{ apply_filter('trim', '   I like Twig.') }}", 'I like Twig.']; }}
 */
class ApplyFilterExtension extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'apply_filter',
                [$this, 'applyFilter'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @param Environment $env
     * @param string $name
     * @param mixed $value
     * @param mixed $parameters
     * @param bool $skipChangeParameters
     *
     * @return string
     *
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function applyFilter(
        Environment $env,
        string $name,
        $value,
        $parameters = [],
        bool $skipChangeParameters = false
    ): string {
        $context = ['value' => $value];

        $template = sprintf('{{ value | %s }}', $name);
        if ($parameters) {
            $contextArguments = null;

            if (is_array($parameters) && !$skipChangeParameters) {
                $index = 0;
                foreach ($parameters as $parameter) {
                    $context['context_'.$index] = $parameter;

                    if ($contextArguments === null) {
                        $contextArguments = 'context_'.$index;
                    } else {
                        $contextArguments .= ', context_'.$index;
                    }

                    $index++;
                }
            } else {
                $contextArguments = 'context';
                $context['context'] = $parameters;
            }

            $template = sprintf('{{ value | %s(%s) }}', $name, $contextArguments);
        }

        return $env->createTemplate($template)->render($context);
    }
}
