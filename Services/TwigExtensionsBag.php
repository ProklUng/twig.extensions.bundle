<?php

namespace Prokl\TwigExtensionsPackBundle\Services;

use Bitrix\Main\EventResult;
use Twig\Environment;

/**
 * Class TwigExtensionsBag
 * @package Prokl\TwigExtensionsPackBundle\Services
 *
 * @since 09.08.2021
 */
class TwigExtensionsBag
{
    /**
     * @var iterable|array $extensions Extensions.
     */
    private $extensions;

    /**
     * TwigExtensionsBag constructor.
     *
     * @param iterable|array $extensions Extensions.
     */
    public function __construct($extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * Обработчик события onAfterTwigTemplateEngineInited.
     *
     * @param Environment $twig Twig.
     *
     * @return EventResult
     */
    public function handle(Environment $twig) : EventResult
    {
        foreach ($this->extensions as $extension) {
            if (!$twig->hasExtension(is_object($extension) ? get_class($extension) : $extension)) {
                $twig->addExtension(is_object($extension) ? $extension : new $extension);
            }
        }

        return new EventResult(
            EventResult::SUCCESS,
            ['engine' => $twig]
        );
    }
}