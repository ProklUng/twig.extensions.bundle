<?php

namespace Prokl\TwigExtensionsPackBundle\Services;

use Prokl\TwigExtensionsPackBundle\Services\Contracts\TwigVariablesInterface;
use Prokl\TwigExtensionsPackBundle\Services\Handlers\AppVariable;

/**
 * Class ConfigureVariables
 * @package Local\Bundles\TimberTwigBundle\Services
 *
 * @since 13.09.2020 Проброс зависимостей снаружи. Инициализация хуков через hooksInit.
 */
class ConfigureVariables
{
    /**
     * @var TwigVariablesInterface[] $twigVariablesHandlers
     */
    private $twigVariablesHandlers;

    /**
     * @var AppVariable $appVariable Транслятор переменных приложения в Твиг.
     */
    private $appVariable;

    /**
     * ConfigureVariables constructor.
     *
     * @param AppVariable $appVariable Variable bag.
     */
    public function __construct(
        AppVariable $appVariable
    ) {
        $this->appVariable = $appVariable;
        $this->config();
    }

    /**
     * Инициализация событий Wordpress.
     *
     * @return void
     */
    public function hooksInit() : void
    {
        add_filter('timber_context', [$this, 'initVariables']);
    }

    /**
     * Инициализация переменных (обработчик события).
     *
     * @param array $data Данные.
     *
     * @return array
     */
    public function initVariables(array $data) : array
    {
        $arExternalVariables =  $this->initExternalTwigContext(...$this->twigVariablesHandlers);

        return array_merge($arExternalVariables, $data);
    }

    /**
     * Конфигурация.
     *
     * @return void
     */
    private function config() : void
    {
        $this->twigVariablesHandlers = [
            $this->appVariable
        ];
    }

    /**
     * Инициализировать кастомные глобальные переменные Twig.
     *
     * @param mixed ...$twigVariables
     *
     * @return array
     */
    private function initExternalTwigContext(...$twigVariables) : array
    {
        if (count($twigVariables) === 0) {
            return [];
        }

        $collection = collect($twigVariables);
        $collectionResult = collect([]);

        $collection->each(
            /**
             * @param TwigVariablesInterface $item
             *
             * @return void
             */
            static function ($item) use (&$collectionResult) : void {
                $data = collect($item->get());
                $collectionResult = $collectionResult->merge($data);
            }
        );

        return $collectionResult->toArray();
    }
}
