<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class Glob
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions
 */
class Glob extends AbstractExtension
{
    /**
     * @var string $root DOCUMENT_ROOT.
     */
    private $root;

    /**
     * Glob constructor.
     *
     * @param string $root DOCUMENT_ROOT.
     */
    public function __construct(string $root)
    {
        $this->root = $root;
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new TwigFilter('glob', function ($pattern, $returnMatch = true) {
                $rootPath = $this->root;

                if ($returnMatch) {
                    $xPattern = explode('*', $pattern);
                }

                $results = [];
                foreach (glob(str_replace('//', '/', "{$rootPath}/{$pattern}")) as $item) {
                    $item = str_replace($rootPath, '', $item);

                    if ($returnMatch) {
                        $match = $item;
                        foreach ($xPattern as $part) {
                            $match = str_replace($part, '', $match);
                        }

                        $results[$match] = $item;
                    } else {
                        $results[] = $item;
                    }
                }

                return $results;
            })
        ];
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'twig_glob';
    }
}
