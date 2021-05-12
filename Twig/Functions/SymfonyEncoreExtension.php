<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Functions;

use Exception;
use Prokl\TwigExtensionsPackBundle\Services\Assets;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class SymfonyEncoreExtension
 * @package Local\Bundles\TwigExtensionsBundle\Twig
 *
 * @since 22.10.2020
 */
class SymfonyEncoreExtension extends AbstractExtension
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
     * Return extension name
     *
     * @return string
     */
    public function getName()
    {
        return 'encore_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('encore_entry_link_tags', [$this, 'entryLinkTag']),
            new TwigFunction('encore_entry_script_tags', [$this, 'entryLinkScript']),
            new TwigFunction('asset', [$this, 'getLinkEntry']),
        ];
    }

    /**
     * encore_entry_link_tags().
     *
     * @param string $entry         Точка входа.
     * @param bool   $addPreloadTag Добавлять link="preload"?
     *
     * @return void
     */
    public function entryLinkTag(string $entry, bool $addPreloadTag = false): void
    {
        $link = $this->getLinkEntry($entry);
        if (!$link) {
            return;
        }

        $finalLink = '<link rel="stylesheet" href="' . $link . '">';

        if ($addPreloadTag) {
            $finalLink = '<link rel="preload" as="style" href="' . $link . '">' . $finalLink;
        }

        echo $finalLink;
    }

    /**
     * encore_entry_script_tags().
     *
     * @param string $entry Точка входа.
     *
     * @return void
     */
    public function entryLinkScript(string $entry): void
    {
        $link = $this->getLinkEntry($entry);
        if (!$link) {
            return;
        }

        echo '<script src="' . $link . '"></script>';
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
        } catch (Exception $e) {
            return '';
        }
    }
}
