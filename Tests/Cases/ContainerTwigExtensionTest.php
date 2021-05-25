<?php

namespace Prokl\TwigExtensionsPackBundle\Tests\Cases;

use Prokl\TwigExtensionsPackBundle\Twig\Extensions\ContainerTwigExtension;
use Prokl\TestingTools\Base\BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Class ContainerTwigExtensionTest
 * @package Prokl\TwigExtensionsPackBundle\Tests\Cases
 * @coversDefaultClass ContainerTwigExtension
 *
 * @since 12.10.2020
 */
class ContainerTwigExtensionTest extends BaseTestCase
{
    /**
     * @var ContainerTwigExtension $obTestObject Тестируемый объект.
     */
    protected $obTestObject;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->obTestObject = new ContainerTwigExtension();
        $this->obTestObject->setContainer($this->getTestContainer());
    }

    /**
    * service().
    *
    * @return void
    */
    public function testService() : void
    {
        $result = $this->obTestObject->service('test.service');

        $this->assertNotEmpty(
            $result,
            'Сервис не обработался.'
        );
    }

    /**
     * service(). Не существующий сервис.
     *
     * @return void
     */
    public function testServiceNotExist() : void
    {
        $this->willSeeException(ServiceNotFoundException::class);

        $this->obTestObject->service($this->faker->slug);
    }

    /**
     * param().
     *
     * @return void
     */
    public function testParam() : void
    {
        $result = $this->obTestObject->param('test.param');

        $this->assertSame(
            'test',
            $result,
            'Параметр не обработался.'
        );
    }

    /**
     * param(). Не существующий параметр.
     *
     * @return void
     */
    public function testParamNotExist() : void
    {
        $this->willSeeException(ParameterNotFoundException::class);

        $this->obTestObject->param($this->faker->slug);
    }

    /**
     * Тестовый контейнер.
     *
     *
     * @return ContainerBuilder
     */
    private function getTestContainer(): ContainerBuilder {

        $container = new ContainerBuilder();
        $class = new class () {};

        $container
            ->register('test.service', get_class($class))
            ->setPublic(true);

        $container->setParameter('test.param', 'test');

        return $container;
    }
}
