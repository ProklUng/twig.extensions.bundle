<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class StringTwigExtension
 * String Twig extension.
 * @package Local\Services\Twig\Extensions
 *
 * @since 11.10.2020
 * @since 24.10.2020 Обновление интерфейса. Убран deprecated Twig_ExtensionInterface.
 * @since 03.11.2020 Чистка.
 */
class StringTwigExtension extends AbstractExtension
{
    /**
     * Return extension name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'twig/strings';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters() : array
    {
        return [
            new TwigFilter('paragraph', [$this, 'paragraph'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
            new TwigFilter('line', [$this, 'line']),
        ];
    }

    /**
     * Add paragraph and line breaks to text.
     *
     * @param string|null $value Строка.
     *
     * @return string
     */
    public function paragraph(?string $value = null): string
    {
        if ($value === null) {
            return '';
        }

        return '<p>' . preg_replace(['~\n(\s*)\n\s*~', '~(?<!</p>)\n\s*~'], ["</p>\n\$1<p>", "<br>\n"], trim($value)) .
            '</p>';
    }

    /**
     * Get a single line.
     *
     * @param string  $value Строка.
     * @param integer $line  Line number (starts at 1)
     *
     * @return string
     */
    public function line($value, $line = 1): string
    {
        if (!$value) {
            return '';
        }

        $lines = explode("\n", $value);

        return $lines[$line - 1] ?? '';
    }
}
