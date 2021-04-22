<?php

namespace Prokl\TwigExtensionsPackBundle\Tests\Cases;

use Exception;
use Prokl\CustomArgumentResolverBundle\Service\ResolversDependency\ResolveDependencyMakerContainerAware;
use InvalidArgumentException;
use Prokl\TwigExtensionsPackBundle\Twig\Extensions\Examples\ExampleSimpleController;
use Prokl\TwigExtensionsPackBundle\Twig\Extensions\RenderExtension;
use Prokl\TestingTools\Base\BaseTestCase;
use Prokl\WpSymfonyRouterBundle\Services\Utils\RouteChecker;
use ReflectionException;
use RuntimeException;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Tests\PHPUnitUtils;
use Tests\Traits\DataProviders\Elements;

/**
 * Class RenderExtensionTest
 * @package Prokl\TwigExtensionsPackBundle\Tests\Cases
 * @coversDefaultClass RenderExtension
 *
 * @since 24.10.2020
 * @since 27.10.2020 Актуализация.
 * @since 01.12.2020 Актуализация.
 */
class RenderExtensionTest extends BaseTestCase
{
    /**
     * @var RenderExtension $obTestObject Тестируемый объект.
     */
    protected $obTestObject;

    private const TEST_ROUTE = '/test/route/';


    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->obTestObject = new RenderExtension(
            $this->container->get(ResolveDependencyMakerContainerAware::class),
            $this->container->get('dispatcher.controller'),
            $this->getRouteCollection(),
        );

        $this->obTestObject->setContainer($this->container);
    }

    /**
     * parseControllerString(). Невалидная строка.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testparseControllerStringInvalidString() : void
    {
        $this->willSeeException(InvalidArgumentException::class);
        PHPUnitUtils::callMethod(
            $this->obTestObject,
            'parseControllerString',
            [$this->faker->slug()]
        );
    }

    /**
     * parseControllerString(). Валидная строка.
     *
     * @param string $classes
     *
     * @dataProvider providerValidControllerString
     *
     * @return void
     * @throws ReflectionException
     */
    public function testparseControllerStringValidString(string $classes) : void
    {
        $result = PHPUnitUtils::callMethod(
            $this->obTestObject,
            'parseControllerString',
            [$classes]
        );

        $this->assertNotEmpty(
            $result,
            'Пустой массив на валидных данных.'
        );

        $this->assertInstanceOf(
            ExampleSimpleController::class,
            $result[0],
            'Не тот класс на валидных данных.'
        );

        if (strpos($classes, 'action2') !== false) {
            $this->assertSame(
                'action2',
                $result[1],
                'Метод контроллера не отработался.'
            );
        } else {
            $this->assertSame(
                'action',
                $result[1],
                'Метод контроллера не отработался.'
            );
        }
    }

    /**
     * render(). Валидный контроллер.
     */
    public function testRenderValidControllerReference() : void
    {
        $controllerReference = new ControllerReference(
            'Prokl\TwigExtensionsPackBundle\Twig\Extensions\Examples\ExampleSimpleController::action2'
        );

        ob_start();
        $this->obTestObject->render($controllerReference);

        $result = ob_get_clean();

        $this->assertSame(
            $result,
            'OK'
        );
    }

    /**
     * render(). Невалидный контроллер.
     */
    public function testRenderInvalidControllerReference() : void
    {
        $this->obTestObject->setContainer($this->container);

        $this->willSeeException(InvalidArgumentException::class);

        $controllerReference = new ControllerReference(
            $this->faker->sentence
        );

        $this->obTestObject->render($controllerReference);
    }

    /**
     * render(). Валидный контроллер.
     */
    public function testRenderValidRoute() : void
    {
        ob_start();
        $this->obTestObject->render(self::TEST_ROUTE);

        $result = ob_get_clean();

        $this->assertSame(
            $result,
            'OK'
        );
    }

    /**
     * render(). Невалидный роут.
     */
    public function testRenderInvalidRoute() : void
    {
        $this->willSeeException(RuntimeException::class);

        $this->obTestObject->render($this->faker->sentence);
    }

    /**
     * Валидные строчки контроллера.
     *
     * @return mixed
     */
    public function providerValidControllerString()
    {
        return $this->provideDataFrom([
            new Elements([
                ExampleSimpleController::class,
                'Prokl\TwigExtensionsPackBundle\Twig\Extensions\Examples\ExampleSimpleController::action',
                'Prokl\TwigExtensionsPackBundle\Twig\Extensions\Examples\ExampleSimpleController::action2',
            ])
        ]);
    }

    /**
     * Тестовая коллекция роутов.
     *
     * @return RouteChecker
     * @throws Exception
     */
    private function getRouteCollection() : RouteChecker
    {
        $route = new Route(
            self::TEST_ROUTE,
            ['_controller' => ExampleSimpleController::class, 'id' => $this->faker->numberBetween(100, 200)]
        );

        $routeCollection = new RouteCollection();

        $routeCollection->add(
          'foo-test',
            $route
        );

        return new RouteChecker(
            $routeCollection,
            $this->container->get('global.request'),
            $this->container->get('request.context'),
        );
    }
}
