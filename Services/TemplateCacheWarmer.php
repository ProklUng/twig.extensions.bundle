<?php

namespace Prokl\TwigExtensionsPackBundle\Services;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Twig\Error\Error;
use Twig\Environment;

/**
 * Class TemplateCacheWarmer
 * @package Prokl\TwigExtensionsPackBundle\Services
 *
 * @since 03.08.2021
 */
class TemplateCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var Environment $router Twig.
     */
    private $twig;

    /**
     * TemplateCacheWarmer constructor.
     *
     * @param Environment $twig Twig.
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @inheritDoc
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function warmUp($cacheDir)
    {
        $paths = $this->twig->getLoader()->getPaths();
        $phpFiles = [];

        foreach ($paths as $path) {
            $files = $this->findTemplatesInDirectory($path);
            foreach ($files as $template) {
                try {
                    $template = $this->twig->load($template);

                    if (\is_callable([$template, 'unwrap'])) {
                        $phpFiles[] = (new \ReflectionClass($template->unwrap()))->getFileName();
                    }
                } catch (Error $e) {
                    // problem during compilation, give up
                    // might be a syntax error or a non-Twig template
                }
            }
        }

        return $phpFiles;
    }

    /**
     * Find templates in the given directory.
     *
     * @param string       $dir        Директория.
     * @param string|null $namespace   Пространство имен.
     * @param array       $excludeDirs Исключенные директории.
     * @return string[]
     */
    private function findTemplatesInDirectory(string $dir, string $namespace = null, array $excludeDirs = []): array
    {
        if (!is_dir($dir)) {
            return [];
        }

        $templates = [];
        foreach (Finder::create()->files()->followLinks()->in($dir)->exclude($excludeDirs) as $file) {
            $templates[] = (null !== $namespace ? '@'.$namespace.'/' : '').str_replace('\\', '/', $file->getRelativePathname());
        }

        return $templates;
    }
}