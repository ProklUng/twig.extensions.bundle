<?php

namespace Prokl\TwigExtensionsPackBundle\Services;

use Bitrix\Main\EventResult;
use Twig\Environment;

/**
 * Class TwigRuntimesBag
 * @package Prokl\TwigExtensionsPackBundle\Services
 *
 * @since 10.08.2021
 */
class TwigRuntimesBag
{
    /**
     * @var iterable|array $runtimes Runtimes.
     */
    private $runtimes;

    /**
     * TwigExtensionsBag constructor.
     *
     * @param iterable|array $runtimes Runtimes.
     */
    public function __construct($runtimes)
    {
        $this->runtimes = $runtimes;
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
        foreach ($this->runtimes as $runtime) {
                $twig->addRuntimeLoader(is_object($runtime) ? $runtime : new $runtime);
        }

        return new EventResult(
            EventResult::SUCCESS,
            ['engine' => $twig]
        );
    }
}