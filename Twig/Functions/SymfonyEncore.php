<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Functions;


use Prokl\TwigExtensionsPackBundle\Services\AssetsService;
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
     * @var AssetsService $assetsService Сервис работы с ассетами.
     */
    private $assetsService;

    /**
     * SymfonyEncore constructor.
     *
     * @param AssetsService $assetsService Сервис работы с ассетами.
     */
    public function __construct(
        AssetsService $assetsService
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
