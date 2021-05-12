<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions\Wordpress;

use ReflectionException;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class RenderController
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions\Wordpress
 *
 * @since 10.05.2021
 */
class RenderController extends AbstractExtension
{
    use ContainerAwareTrait;

    /**
     * Return extension name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'twig/render-controller';
    }

    /**
     * Функции.
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('renderController', [$this, 'renderController']),
        ];
    }

    /**
     * Рендер из контроллера.
     *
     * @param string $className Название класса контроллера.
     * @param mixed  $params    Произвольное количество параметров.
     *
     * @return string
     *
     * @since 02.10.2010 Разрешение контроллера из контейнера.
     */
    public function renderController(string $className, ...$params): string
    {
        $parsedClassName = $className;
        /** Метод по умолчанию. */
        $method = '::action';
        /** Чистое название метода. */
        $clearMethod = 'action';
        // Если в строке имени класса встречается ::, то попытаемся получить название класса и метода.
        if (strpos($className, '::') !== false) {
            $arParsed = explode('::', $className);
            $parsedClassName = trim($arParsed[0]);
            $method = '::'.trim($arParsed[1]);
            $clearMethod = trim($arParsed[1]);
        }

        // Пытаемся получить из контейнера.
        if ($this->container->has($className)) {
            $resolvedInstance = $this->container->get($className);
            if ($resolvedInstance !== null
                &&
                method_exists($resolvedInstance, $clearMethod)) {
                ob_start();
                $resolvedInstance->$clearMethod(...$params);

                $content = ob_get_clean();

                return $content ?: '';
            }
        }

        /** Полная строка namespace + класс + статический метод. */
        $tryAction = $parsedClassName . $method;

        // Вызов не статического метода класса контроллера.
        try {
            if (!$this->isStaticClassMethod($parsedClassName, $clearMethod)
                && class_exists($parsedClassName)
                /** @psalm-suppress MixedMethodCall */
                && method_exists($resolvedInstance = new $parsedClassName, $clearMethod)
            ) {
                ob_start();
                $resolvedInstance->$clearMethod(...$params);

                $content = ob_get_clean();

                return $content ?: '';
            }
        } catch (ReflectionException $e) {
            return '';
        }

        if (is_callable($tryAction)) {
            return (string)$tryAction(...$params);
        }

        return '';
    }

    /**
     * Проверка - метод статический?
     *
     * @param string $class  Полноразмерное название класса.
     * @param string $method Метод.
     *
     * @return boolean
     * @throws ReflectionException
     */
    private function isStaticClassMethod(string $class, string $method): bool
    {
        if (!class_exists($class)) {
            throw new ReflectionException(
                'Class ' . $class . ' not exist.'
            );
        }

        $reflection = new ReflectionMethod($class, $method);

        return $reflection->isStatic();
    }
}