<?php

namespace Prokl\TwigExtensionsPackBundle\Services\Runtime;

use Symfony\Contracts\Cache\CacheInterface;
use Twig\Extra\Cache\CacheRuntime;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

/**
 * Class CacheRuntimeLoader
 *
 * @package Prokl\TwigExtensionsPackBundle\Services\Runtime
 * @since 10.08.2021
 */
class CacheRuntimeLoader implements RuntimeLoaderInterface
{
    /**
     * @var CacheInterface $cacher Кэшер.
     */
    private $cacher;

    /**
     * CacheRuntimeLoader constructor.
     *
     * @param CacheInterface $cacher Кэшер.
     */
    public function __construct(CacheInterface $cacher)
    {
        $this->cacher = $cacher;
    }

    /**
     * @inheritdoc
     */
    public function load($class)
    {
        if (CacheRuntime::class === $class) {
            return new CacheRuntime($this->cacher);
        }
    }
}
