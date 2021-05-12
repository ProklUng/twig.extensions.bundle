<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions\Bitrix;

use Twig\Extension\AbstractExtension;

/**
 * Class BitrixExtension. Расширение, которое добавляет глобалки php в шаблоны
 *
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions\Bitrix
 */
class PhpGlobalsExtension extends AbstractExtension
{
    /**
     * @return string
     */
    public function getName() : string
    {
        return 'php_globals_extension';
    }

    /**
     * @return array[]
     */
    public function getGlobals() : array
    {
        return [
            '_SERVER'       => $_SERVER,
            '_REQUEST'      => $_REQUEST,
            '_GET'          => $_GET,
            '_POST'         => $_POST,
            '_FILES'        => $_FILES,
            '_SESSION'      => $_SESSION,
            '_COOKIE'       => $_COOKIE,
            '_GLOBALS'      => $GLOBALS,
        ];
    }
}