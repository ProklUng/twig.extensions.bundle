<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class JsonDecodeExtension
 * json_decode in Twig.
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions
 *
 * @since 23.10.2020
 * @since 24.10.2020 Обновление интерфейса. Убран deprecated Twig_ExtensionInterface.
 * @since 03.11.2020 Чистка.
 */
class JsonDecodeExtension extends AbstractExtension
{
    /**
     * Return extension name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'twig/json-decode';
    }

    /**
     * @inheritDoc
     *
     * @return TwigFilter[]
     */
    public function getFilters() : array
    {
        return [
            new TwigFilter('json_decode', [$this, 'jsonDecode'])
        ];
    }

    /**
     * Json_decode.
     *
     * @param string  $string     Строка.
     * @param boolean $assocArray В ассоциативный массив?
     *
     * @return mixed
     */
    public function jsonDecode(string $string, $assocArray = true)
    {
        return json_decode($string, $assocArray, 512);
    }
}
