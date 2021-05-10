<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions\Wordpress;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class WordpressExtension
 * Expose Wordpress functions to Twig.
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions\Wordpress
 *
 * @since 09.11.2020
 */
class WordpressExtension extends AbstractExtension
{
    /**
     * Return extension name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'wordpress-functions';
    }

    /**
     * Функции.
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction( 'fn', [$this, 'execFunction'] ),
            new TwigFunction( 'bloginfo', 'bloginfo'),
            new TwigFunction('language_attributes',
                function () : string {
                    return $this->getOutput('language_attributes');
                },
                ['is_safe' => ['html']]
            ),
            new TwigFunction('get_site_url',
                static function () : string {
                    return get_site_url();
                },
                ['is_safe' => ['html']]
            ),
            new TwigFunction('wp_head',
                function () : string {
                    return $this->getOutput('wp_head');
                },
                ['is_safe' => ['html']]
            ),
            new TwigFunction('wp_footer',
                function ()  : string {
                    return $this->getOutput('wp_footer');
                }, ['is_safe' => ['html']]
            ),
            new TwigFunction('body_class',
                function () : string {
                    return $this->getOutput('body_class');
                }, ['is_safe' => ['html']]
            ),
            new TwigFunction('get_header',
                function () : string {
                    return $this->getOutput('get_header');
                }, ['is_safe' => ['html']]
            ),
            new TwigFunction('get_footer',
                function () : string {
                    return $this->getOutput('get_footer');
                }, ['is_safe' => ['html']]
            ),
            new TwigFunction('get_post_format',
                function () : string {
                    return $this->getOutput('get_post_format');
                }, ['is_safe' => ['html']]
            ),
            new TwigFunction('wp_link_pages',
                function () : string {
                    return $this->getOutput('wp_link_pages');
                }, ['is_safe' => ['html']]
            ),
            new TwigFunction('ajax_url',
                function () : string {
                    return admin_url('admin-ajax.php');
                }, ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * Return function echo.
     *
     * @param mixed $function Функция.
     * @param array $args     Аргументы.
     *
     * @return string
     */
    private function getOutput($function, $args = []): string
    {
        ob_start();
        call_user_func_array($function, $args);

        return (string)ob_get_clean();
    }

    /**
     * @param mixed $function_name Название функции.
     *
     * @return mixed
     */
    public function execFunction($function_name)
    {
        $args = func_get_args();

        array_shift($args);

        if (is_string($function_name)) {
            $function_name = trim($function_name);
        }

        return call_user_func_array($function_name, ($args));
    }
}
