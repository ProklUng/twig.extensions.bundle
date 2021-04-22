<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class ArrayExtension
 * Expose PHP's array functions to Twig.
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions
 *
 * @since 11.10.2020 Пример Twig extension.
 * @since 24.10.2020 Обновление интерфейса. Убран deprecated Twig_ExtensionInterface.
 * @since 03.11.2020 Чистка.
 */
class ArrayExtension extends AbstractExtension
{
    /**
     * Return extension name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'jasny/array';
    }

    /**
     * Callback for Twig
     * @ignore
     *
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('sum', [$this, 'sum']),
            new TwigFilter('product', [$this, 'product']),
            new TwigFilter('values', [$this, 'values']),
            new TwigFilter('as_array', [$this, 'asArray']),
            new TwigFilter('html_attr', [$this, 'htmlAttributes']),
        ];
    }

    /**
     * Calculate the sum of values in an array
     *
     * @param array|null $array
     *
     * @return integer|null
     */
    public function sum($array): ?int
    {
        return $array !== null ? (int)array_sum($array) : null;
    }

    /**
     * Calculate the product of values in an array
     *
     * @param array|null $array
     *
     * @return integer|null
     */
    public function product($array): ?int
    {
        return $array !== null ? (int)array_product($array) : null;
    }

    /**
     * Return all the values of an array or object
     *
     * @param array|object|null $array
     *
     * @return array
     */
    public function values($array): ?array
    {
        return $array !== null ? array_values((array)$array) : null;
    }

    /**
     * Cast value to an array
     *
     * @param object|mixed $value
     *
     * @return array
     */
    public function asArray($value): array
    {
        return is_object($value) ? get_object_vars($value) : (array)$value;
    }

    /**
     * Cast an array to an HTML attribute string
     *
     * @param mixed $array
     *
     * @return string
     */
    public function htmlAttributes($array): ?string
    {
        if (!$array) {
            return null;
        }

        $str = '';
        foreach ($array as $key => $value) {
            if ($value === null || $value === false) {
                continue;
            }

            if ($value === true) {
                $value = $key;
            }

            $str .= ' ' . $key . '="' . addcslashes($value, '"') . '"';
        }

        return trim($str);
    }
}
