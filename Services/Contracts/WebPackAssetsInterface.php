<?php

namespace Prokl\TwigExtensionsPackBundle\Services\Contracts;

use Prokl\TwigExtensionsPackBundle\Services\Exceptions\WebpackUtilException;

/**
 * Interface WebPackAssetsInterface
 * @package Prokl\TwigExtensionsPackBundle\Services\Contracts
 */
interface WebPackAssetsInterface
{
    /**
     * Получить путь до файла-ассета по его имени.
     *
     * @param string $entryName Имя файла-ассета.
     *
     * @return string                Путь до ассета.
     * @throws WebpackUtilException  Исключения работы с WebPack.
     */
    public function getEntry(string $entryName): string;
}
