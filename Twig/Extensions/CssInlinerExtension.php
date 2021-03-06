<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class CssInlinerExtension
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions
 *
 * @since 31.10.2020
 */
class CssInlinerExtension extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getFilters()
    {
        return [
            new TwigFilter('inline_css', [$this, 'twig_inline_css'], ['is_safe' => ['all']]),
        ];
    }

    /**
     * @param string $body
     * @param string ...$css
     *
     * @return string
     */
    public function twig_inline_css(string $body, string ...$css): string
    {
        static $inliner;
        if (null === $inliner) {
            $inliner = new CssToInlineStyles();
        }

        return (string)$inliner->convert($body, implode("\n", $css));
    }
}
