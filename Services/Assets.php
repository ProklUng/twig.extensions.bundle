<?php

namespace Prokl\TwigExtensionsPackBundle\Services;

use Exception;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Assets
 * @package Prokl\TwigExtensionsPackBundle\Services
 *
 * Хелпер для работы с манифест-файлом Вебпака.
 *
 * @since 08.11.2020 Получение entrypoints.
 */
class Assets
{
    /**
     * @var string
     */
    private $base;

    /**
     * @var string
     */
    private $manifestFile;

    /**
     * @var array
     */
    private $manifest;

    /**
     * @var array $entrypoints Содержимое entrypoints.json.
     */
    private $entrypoints;

    /**
     * Assets constructor.
     *
     * @param string $base         Расположение директории ассетов относительно
     *                             DOCUMENT_ROOT.
     * @param string $manifestFile Имя манифест-файла.
     *
     * @throws Exception Стандартное исключение.
     */
    public function __construct(string $base = 'local/build/', string $manifestFile = 'manifest.json')
    {
        $this->base = $base;
        $this->manifestFile = $manifestFile;

        $this->loadManifest();
    }

    /**
     * Получить путь до файла-ассета по его имени
     *
     * @param string $entryName Имя файла-ассета.
     * @return string Путь до ассета.
     * @throws Exception Стандартное исключение.
     */
    public function getEntry(string $entryName)
    {
        $entryPath = $this->base . $entryName;
        $entry = $this->manifest[ $entryPath ];

        if (is_null($entry)) {
            throw new Exception('Entry `' . $entryPath .'` not found in manifest file!');
        }

        return $entry;
    }

    /**
     * Данные на entrypoint js из entrypoints.js.
     *
     * @param string $entrypoint Entrypoint.
     *
     * @return array
     *
     * @throws InvalidArgumentException
     *
     * @since 08.11.2020
     */
    public function getWebpackJsFiles($entrypoint)
    {
        if (!array_key_exists($entrypoint, $this->getEntrypoints())) {
            throw new InvalidArgumentException(sprintf('Invalid entrypoint "%s"', $entrypoint));
        }

        $files = $this->getEntrypoints()[$entrypoint];
        if (!array_key_exists('js', $files)) {
            return [];
        }

        return $files['js'];
    }

    /**
     * Данные на entrypoint css из entrypoints.js.
     *
     * @param string $entrypoint Entrypoint.
     *
     * @return array
     *
     * @throws InvalidArgumentException
     *
     * @since 08.11.2020
     */
    public function getWebpackCssFiles($entrypoint)
    {
        if (!array_key_exists($entrypoint, $this->getEntrypoints())) {
            throw new InvalidArgumentException(sprintf('Invalid entrypoint "%s"', $entrypoint));
        }

        $files = $this->getEntrypoints()[$entrypoint];
        if (!array_key_exists('css', $files)) {
            return [];
        }

        return $files['css'];
    }

    /**
     * Путь к сборке Webpack в зависимости от окружения.
     *
     * @param string $debug         Прод или нет.
     * @param string $pathDevBuild  Dev сборка.
     * @param string $pathProdBuild Продакшен сборка.
     *
     * @return string
     */
    public static function pathBuild(
        string $debug,
        string $pathDevBuild,
        string $pathProdBuild
    ) {
        return $debug ? $pathDevBuild : $pathProdBuild;
    }

    /**
     * Генерирует массив ассетов на основе файла манифеста.
     *
     * @return void
     * @throws Exception Стандартное исключение.
     */
    private function loadManifest()
    {
        $manifest = json_decode(file_get_contents(
            $_SERVER['DOCUMENT_ROOT'] . '/' . $this->base . $this->manifestFile
        ), true);

        if (! (bool) $manifest) {
            throw new Exception('Manifest file not found!');
        }

        $this->manifest = $manifest;
    }

    /**
     * Entrypoints.
     *
     * @return array
     * @throws RuntimeException Ошибки чтения и декодирования entrypoints.json.
     *
     * @since 08.11.2020
     */
    private function getEntrypoints(): array
    {
        if (count($this->entrypoints) === 0) {
            $file = $this->base . DIRECTORY_SEPARATOR . 'entrypoints.json';
            $content = file_get_contents($file);
            if ($content === false) {
                throw new RuntimeException(\sprintf('Unable to read file "%s"', $file));
            }

            $json = json_decode($content, true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new RuntimeException(\sprintf('Unable to decode json file "%s"', $file));
            }

            $this->entrypoints = (array)$json['entrypoints'];
        }

        return $this->entrypoints;
    }
}
