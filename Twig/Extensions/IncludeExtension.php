<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use Symfony\Component\Filesystem\Filesystem;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class IncludeExtension.
 *
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions
 */
class IncludeExtension extends AbstractExtension
{
    /**
     * @var string $documentRoot DOCUMENT_ROOT.
     */
    private $documentRoot;

    /**
     * @var Filesystem Файловая система.
     */
    private $filesystem;

    /**
     * IncludeExtension constructor.
     *
     * @param string     $documentRoot DOCUMENT_ROOT.
     * @param Filesystem $filesystem   Файловая система.
     */
    public function __construct(
        string $documentRoot,
        Filesystem $filesystem
    ) {
        $this->documentRoot = $documentRoot;
        $this->filesystem = $filesystem;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'twig_php_include';
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('include', [$this, 'includeFile']),
        ];
    }

    /**
     * @param string $path Путь к файлу относительно DOCUMENT_ROOT.
     *
     * @return string
     */
    public function includeFile(string $path) : string
    {
        if (!$this->filesystem->exists($this->documentRoot . $path)) {
            return '';
        }

        ob_start();
        /** @noinspection PhpIncludeInspection */
        include $this->documentRoot . $path;
        $content = ob_get_clean();

        return (string)$content;
    }
}