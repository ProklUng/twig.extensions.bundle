<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Functions;

use reflectionClass;
use ReflectionException;
use RuntimeException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TwigStaticReflection
 * @package Prokl\TwigExtensionsPackBundle\Twig\Functions
 *
 * @since 07.06.2021
 *
 * @internal
 * {{ get_static("Prokl\\Bundle\\EntityBundle\\Badge", 'propertyName') }}
 */
class TwigStaticReflection extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('call_static', [$this, 'callStaticMethod']),
            new TwigFunction('get_static', [$this, 'getStaticProperty']),
        );
    }

    /**
     *
     * Вызвать статический метод.
     *
     * @param string $class  Класс.
     * @param string $method Метод.
     * @param array  $args   Аргументы.
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function callStaticMethod(string $class, string $method, array $args = [])
    {
        $reflection = new reflectionClass($class);

        if ($reflection->hasMethod($method)
            && $reflection->getMethod($method)->isStatic()
            && $reflection->getMethod($method)->isPublic()) {
            return call_user_func_array($class.'::'.$method, $args);
        }

        throw new RuntimeException(
            sprintf('Invalid static method call for class %s and method %s', $class, $method)
        );
    }

    /**
     * Получить статическое свойство.
     *
     * @param string $class    Класс.
     * @param string $property Свойство.
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function getStaticProperty(string $class, string $property)
    {
        $reflection = new reflectionClass($class);

        if ($reflection->hasProperty($property)
            && $reflection->getProperty($property)->isStatic()
            && $reflection->getProperty($property)->isPublic()) {
            return $reflection->getProperty($property)->getValue();
        }

        throw new RuntimeException(
            sprintf('Invalid static property get for class %s and property %s', $class, $property)
        );
    }
}
