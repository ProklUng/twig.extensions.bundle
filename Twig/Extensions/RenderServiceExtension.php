<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use ReflectionException;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Twig\Extension\AbstractExtension;
use ReflectionMethod;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RenderServiceExtension
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions
 *
 * @since 17.01.2021
 *
 * @example
 * {{ render_service('app.controller.user', 'detail',
 * {'user': user},
 * {'eventDispatcher': 'event_dispatcher'}
 * ) }}
 */
class RenderServiceExtension extends AbstractExtension
{
    use ContainerAwareTrait;

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_service', [$this, 'renderService'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string $service
     * @param string $method
     * @param array  $parameters
     * @param array  $services
     *
     * @return string
     * @throws ReflectionException|RuntimeException
     */
    public function renderService(
        string $service,
        string $method,
        array $parameters = [],
        array $services = []
    ): string {
        if (!$this->container->has($service)) {
            throw new RuntimeException(
                sprintf(
                    'Service %s not exists.',
                    $service
                )
            );
        }

        $serviceInstance = $this->container->get($service);
        if ($serviceInstance === null) {
            return '';
        }

        $injectParameters = [];

        /**
         * @var string|integer $key
         * @var mixed          $value
         */
        foreach ($services as $key => $value) {
            if (is_string($value) && $this->container->has($value)) {
                $injectParameters[$key] = $this->container->get($value);
            }
        }

        $parameters = $this->orderParameters(
            get_class($serviceInstance),
            $method,
            array_replace($injectParameters, $parameters)
        );

        $response = call_user_func_array([$serviceInstance, $method], $parameters);
        if ($response instanceof Response) {
            $response = $response->getContent();
        }

        if (!is_string($response)) {
            return '';
        }

        return $response;
    }

    /**
     * @param string $class
     * @param string $method
     * @param array  $parameters
     *
     * @return array
     * @throws ReflectionException
     */
    private function orderParameters(
        string $class,
        string $method,
        array $parameters
    ): array {

        if (!class_exists($class)) {
            throw new ReflectionException('Class' . $class . ' not found.');
        }

        $reflectionMethod = new ReflectionMethod($class, $method);
        $reflectionParameters = $reflectionMethod->getParameters();

        $injectParameters = [];
        foreach ($reflectionParameters as $reflectionParameter) {
            $refName = $reflectionParameter->getName();
            if (!array_key_exists($refName, $parameters) && $reflectionParameter->isDefaultValueAvailable()) {
                /** @psalm-suppress MixedAssignment */
                $injectParameters[$refName] = $reflectionParameter->getDefaultValue();
            } else {
                /** @psalm-suppress MixedAssignment */
                $injectParameters[$refName] = $parameters[$refName];
            }
        }

        return $injectParameters;
    }
}
