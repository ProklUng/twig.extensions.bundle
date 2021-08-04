<?php

namespace Prokl\TwigExtensionsPackBundle\Services\Contracts;

/**
 * Interface TwigVariablesInterface
 * @package Prokl\TwigExtensionsPackBundle\Services\Contracts
 */
interface TwigVariablesInterface
{
    /**
     * Получить массив с переменными.
     *
     * @return array
     */
    public function get() : array;
}
