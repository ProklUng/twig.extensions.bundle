<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Functions;

use Exception;
use Prokl\TwigExtensionsPackBundle\Services\Assets;
use Prokl\TwigExtensionsPackBundle\Services\Exceptions\WebpackUtilException;

/**
 * Class SymfonyEncore
 * @package Prokl\TwigExtensionsPackBundle\Twig\Functions
 *
 * @since 14.10.2020 Выпиливание auto-wiring.
 */
class SymfonyEncore
{
    /**
     * @var Assets $assetsService Сервис работы с ассетами.
     */
    private $assetsService;

    /**
     * SymfonyEncore constructor.
     *
     * @param Assets $assetsService Сервис работы с ассетами.
     */
    public function __construct(
        Assets $assetsService
    ) {
        $this->assetsService = $assetsService;
    }

    /**
     * encore_entry_link_tags().
     *
     * @param string  $entry         Точка входа.
     * @param boolean $addPreloadTag Добавлять link="preload"?
     *
     * @return string
     */
    public function entryLinkTag(string $entry, bool $addPreloadTag = false): string
    {
        $link = $this->getLinkEntry($entry);
        if (!$link) {
            return $link;
        }

        $finalLink = '<link rel="stylesheet" href="' . $link . '">';

        if ($addPreloadTag) {
            $finalLink = '<link rel="preload" as="style" href="' . $link . '">' . $finalLink;
        }

        return $finalLink;
    }

    /**
     * encore_entry_script_tags().
     *
     * @param string $entry Точка входа.
     *
     * @return string
     */
    public function entryLinkScript(string $entry): string
    {
        $link = $this->getLinkEntry($entry);
        if (!$link) {
            return $link;
        }

        return '<script src="' . $link . '"></script>';
    }

    /**
     * Получить ссылку на ассет из манифеста.
     *
     * @param string $entry Точка входа.
     *
     * @return string
     * @throws Exception
     */
    public function getLinkEntry(string $entry): string
    {
        try {
            return $this->assetsService->getEntry($entry);
        } catch (WebpackUtilException $e) {
            return '';
        }
    }
}
