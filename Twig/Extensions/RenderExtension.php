<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use InvalidArgumentException;
use Prokl\WpSymfonyRouterBundle\Services\Utils\DispatchController;
use Prokl\WpSymfonyRouterBundle\Services\Utils\RouteChecker;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class RenderExtension
 * Расширение Twig - команда render().
 * @package Local\Bundles\TwigExtensionsPackBundle\Twig\Extensions
 *
 * @since 21.10.2020
 * @since 24.10.2020 Обновление интерфейса. Убран deprecated Twig_ExtensionInterface.
 * @since 03.11.2020 Чистка.
 * @since 01.12.2020 Внедрение сервиса routing.utils.
 * @since 06.12.2020 Рефакторинг.
 * @since 22.01.2021 Классы получаются только из контейнера, без автоматического разрешения зависимостей.
 */
class RenderExtension extends AbstractExtension
{
    use ContainerAwareTrait;

    /**
     * @var DispatchController $dispatchController Исполнитель контроллеров.
     */
    private $dispatchController;

    /**
     * @var RouteChecker $routingService Утилиты по работе с роутингом.
     */
    private $routingService;

    /**
     * RenderExtension constructor.
     *
     * @param DispatchController $dispatchController Исполнитель контроллеров.
     * @param RouteChecker       $routing            Утилиты по работе с роутами.
     */
    public function __construct(
        DispatchController $dispatchController,
        RouteChecker $routing
    ) {
        $this->dispatchController = $dispatchController;
        $this->routingService = $routing;
    }

    /**
     * Return extension name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'render_extension';
    }

    /**
     * Twig functions
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render', [$this, 'render']),
        ];
    }

    /**
     * Twig команда render().
     *
     * @param ControllerReference|string $controller Референс контроллера.
     * @param array                      $options    Опции.
     *
     * @return void
     *
     * @throws RuntimeException Не удалось найти роут.
     */
    public function render($controller, array $options = []) : void
    {
        // Если в options присутствует ключ headers, то считаем, что это заголовки запроса.
        if (array_key_exists('headers', $options)) {
            $this->dispatchController->setHeaders($options['headers']);
            unset($options['headers']); // Чтобы не замусоривать дальнейшее использование опций.
        }

        if ($controller instanceof ControllerReference) {
            $resolvedInboundController = $controller;
        } else {
            // Получить данные из url роута.
            $resolvedInboundController = $this->routingService->getRouteInfoReference($controller, $options);

            if ($resolvedInboundController === null) {
                throw new RuntimeException(
                    sprintf(
                        'Twig function render: error rendering route %s.',
                        $controller
                    )
                );
            }
        }

        $controllerClass = $resolvedInboundController->controller;
        $attributes = $resolvedInboundController->attributes;
        $query = $resolvedInboundController->query;

        $resolvedController = $this->parseControllerString($controllerClass);

        $this->dispatchController->setParams($attributes)
            ->setQuery($query);

        if ($this->dispatchController->dispatch($resolvedController)) {
            $response = $this->dispatchController->getResponse();
            if (!$response) {
                echo '';
                return;
            }

            $content =  (string)$response->getContent();

            // Ответ может быть зазипован.
            $isGzipped = (mb_strpos($content, "\x1f" . "\x8b" . "\x08") === 0);
            if ($isGzipped) {
                $content = gzdecode($content);
            }
            echo $content;

            return;
        }

        throw new RuntimeException(
            sprintf(
                sprintf(
                    'Twig function render: error rendering controller %s.',
                    $controllerClass
                )
            )
        );
    }

    /**
     * Распарсить строку с контроллером и методом.
     *
     * @param string $controller Строка с контроллером.
     *
     * @return array
     *
     * @throws InvalidArgumentException Несуществующие классы и методы.
     */
    private function parseControllerString(string $controller): array
    {
        if (strpos($controller, '::') !== false) {
            [$class, $method] = explode('::', $controller, 2);

            $resolvedControllerClass = $this->getFromContainer($class);

            $this->checkClassAndMethod($resolvedControllerClass, $class, $method);

            return [$resolvedControllerClass, $method];
        }

        $resolvedControllerClass = $this->getFromContainer($controller);

        $methodDefault = 'action';
        if ($resolvedControllerClass !== null && method_exists($resolvedControllerClass, '__invoke')) {
            $methodDefault = '__invoke';
        }

        $this->checkClassAndMethod($resolvedControllerClass, $controller, $methodDefault);

        return [$resolvedControllerClass, $methodDefault];
    }

    /**
     * Проверка на существование класса и метода контроллера.
     *
     * @param mixed  $resolvedControllerClass Уже отресолвленный класс или строка.
     * @param string $parsedClassName         Класс.
     * @param string $method                  Метод.
     *
     * @return void
     *
     * @throws InvalidArgumentException Несуществующие классы и методы.
     */
    private function checkClassAndMethod($resolvedControllerClass, string $parsedClassName, string $method) : void
    {
        if (!$resolvedControllerClass) {
            throw new InvalidArgumentException(
                sprintf(
                    'class %s not resolved.',
                    $parsedClassName
                )
            );
        }

        if (!method_exists($resolvedControllerClass, $method)) {
            throw new InvalidArgumentException(
                sprintf(
                    'method %s not exist in class %s.',
                    $method, $parsedClassName
                )
            );
        }
    }

    /**
     * Получить сервис из контейнера.
     *
     * @param string $class Класс, предполагаемый сервисом.
     *
     * @return object|null
     */
    private function getFromContainer(string $class)
    {
        if ($this->container->has($class)) {
            return $this->container->get($class);
        }

        return null;
    }
}
