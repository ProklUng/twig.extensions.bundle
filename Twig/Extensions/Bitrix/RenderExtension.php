<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions\Bitrix;

use InvalidArgumentException;
use Prokl\BitrixSymfonyRouterBundle\Services\Contracts\DispatchControllerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class RenderExtension
 * Расширение Twig - команда render().
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions\Bitrix
 *
 * @since 21.10.2020
 *
 * Пример использования в твиговском шаблоне:
 *
 * <div id="sidebar">
 * {{ render(controller(
 * 'App\\Controller\\ArticleController::recentArticles',
 * { 'max': 3 }
 * )) }}
 * </div>
 *
 * Контроллер может обозначаться по разному:
 *
 * 1. 'App\\Controller\\ArticleController'. Проверяется наличие __invoke. Потом метода action.
 * 2. 'App\\Controller\\ArticleController::recentArticles'. Класс App\Controller\ArticleController,
 * метод recentArticles.
 */
class RenderExtension extends AbstractExtension
{
    use ContainerAwareTrait;

    /**
     * @var DispatchControllerInterface $dispatchController Исполнитель контроллеров.
     */
    private $dispatchController;

    /**
     * @var RouteCollection $routeCollection Коллекция роутов.
     */
    private $routeCollection;

    /**
     * RenderExtension constructor.
     *
     * @param DispatchControllerInterface $dispatchController Исполнитель контроллеров.
     * @param RouteCollection             $routeCollection    Коллекция роутов.
     */
    public function __construct(
        DispatchControllerInterface $dispatchController,
        RouteCollection $routeCollection
    ) {
        $this->dispatchController = $dispatchController;
        $this->routeCollection = $routeCollection;
    }

    /**
     * Return extension name
     *
     * @return string
     */
    public function getName()
    {
        return 'render_extension';
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('render', [$this, 'render']),
        ];
    }

    /**
     * Twig команда render().
     *
     * @param ControllerReference|string $controller Референс контроллера или URL.
     * @param array                      $options    Опции.
     *
     * @return void
     *
     * @throws RuntimeException Не удалось найти роут.
     */
    public function render($controller, array $options = []) : void
    {
        // Если в options присутствует ключ headers, то считаем, что это заголовки запроса.
        if (!empty($options['headers'])) {
            $this->dispatchController->setHeaders($options['headers']);
            unset($options['headers']); // Чтобы не замусоривать дальнейшее использование опций.
        }

        if ($controller instanceof ControllerReference) {
            $resolvedInboundController = $controller;
        } else {
            // Получить данные из url роута.
            $resolvedInboundController = $this->getRouteInfo($controller, $options);

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
            $content =  (string)$response->getContent();

            // Ответ может быть зазипован.
            $isGzipped = mb_strpos($content, "\x1f" . "\x8b" . "\x08") === 0;
            if ($isGzipped) {
                $content = gzdecode($content);
            }
            echo (string)$content;

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
     * @throws InvalidArgumentException Что-то не то с аргументами.
     */
    private function parseControllerString(string $controller)
    {
        if (strpos($controller, '::') !== false) {
            $parsedClass = explode('::', $controller);
            if (($resolvedControllerClass = $this->getFromContainer($parsedClass[0])) === null) {
                throw new InvalidArgumentException(
                    'Service ' .  $parsedClass[0] . ' not exists. Forget marked his?'
                );
            }

            $this->checkClassAndMethod($resolvedControllerClass, $parsedClass[0], $parsedClass[1]);

            return [$resolvedControllerClass, $parsedClass[1]];
        }

        if (($resolvedControllerClass = $this->getFromContainer($controller)) === null) {
            throw new InvalidArgumentException(
                'Service ' .  $controller . ' not exists. Forget marked his?'
            );
        }

        $methodDefault = 'action';
        if (method_exists($resolvedControllerClass, '__invoke')) {
            $methodDefault = '__invoke';
        }

        $this->checkClassAndMethod($resolvedControllerClass, $controller, $methodDefault);

        return [$resolvedControllerClass, $methodDefault];
    }

    /**
     * Получить информацию о роуте по URL.
     *
     * @param string $uri     URL.
     * @param array  $options Опции.
     *
     * @return ControllerReference|null
     */
    private function getRouteInfo(string $uri, array $options = []) : ?ControllerReference
    {
        $matcher = new UrlMatcher($this->routeCollection, new RequestContext());

        try {
            $routeData = $matcher->match($uri);

            $controllerRoute = (string)$routeData['_controller'];
            unset($routeData['_controller'], $routeData['_route']);

            return new ControllerReference(
                $controllerRoute,
                array_merge($routeData, $options)
            );
        } catch (ResourceNotFoundException | MethodNotAllowedException $e) {
            return null;
        }
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
     * @throws InvalidArgumentException Что-то не то с аргументами.
     */
    private function checkClassAndMethod($resolvedControllerClass, string $parsedClassName, string $method): void
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
                    $method,
                    $parsedClassName
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
