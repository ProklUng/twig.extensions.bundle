<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class YouTubeExtension
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions
 *
 * @fork https://github.com/ShadeSoft/TwigVideoBox/blob/master/Twig/YouTubeExtension.php
 */
class YouTubeExtension extends AbstractExtension
{
    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new TwigFilter('youtube', [$this, 'getBox'], ['is_safe' => ['html']]),
            new TwigFilter('youtubeBoxes', [$this, 'getBoxes'])
        ];
    }

    /**
     * @param string         $id     YoutubeId.
     * @param integer|string $width  Ширина.
     * @param integer|string $height Высота.
     *
     * @return string
     */
    public function getBox($id, $width = 560, $height = 315)
    {
        return '<iframe width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' .
                $id . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
    }

    /**
     * @param string         $content Контент.
     * @param integer|string $width   Ширина.
     * @param integer|string $height  Высота.
     *
     * @return string
     */
    public function getBoxes(string $content, $width = 560, $height = 315)
    {
        return preg_replace(
            '#([^\"\'])http[s]?://([w]{3}\.)?youtu[\.]?be(\.com)?/(watch\?v=)?([_a-zA-Z0-9-]+)#',
            '${1}<iframe width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/${5}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>',
            $content
        );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'twig_youtube_extension';
    }
}
