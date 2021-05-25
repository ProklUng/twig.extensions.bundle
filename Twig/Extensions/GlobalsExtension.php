<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use Exception;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * Class GlobalsExtension
 * Глобальные переменные. Секция globals конфигурации twig.
 * @package Local\Bundles\TwigExtensionsPackBundle\Twig\Extensions
 *
 * @since 17.02.2021
 */
class GlobalsExtension extends AbstractExtension implements GlobalsInterface
{
    use ContainerAwareTrait;

    /**
     * @var array $config Конфигурация Twig.
     */
    private $config;

    /**
     * GlobalsExtension constructor.
     *
     * @param array $twigConfig Конфигурация Twig.
     *
     * @throws Exception
     */
    public function __construct(
        array $twigConfig
    ) {
        $this->config = $twigConfig;
    }

    /**
     * Return extension name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'twig/globals';
    }

    /**
     * @inheritDoc
     */
    public function getGlobals() : array
    {
        $result = [];

        /**
         * @var string $name
         * @var mixed  $global
         */
        foreach ($this->config as $name => $global) {
            if (!$global) {
                continue;
            }

            if (is_string($global) && $this->container->has($global)) {
                $result[$name] = $this->container->get($global);

                continue;
            }

            $result[$name] = $global;
        }

        return $result;
    }
}

