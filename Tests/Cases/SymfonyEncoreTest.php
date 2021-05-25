<?php

namespace Prokl\TwigExtensionsPackBundle\Tests\Cases;

use Mockery;
use Prokl\TestingTools\Base\BaseTestCase;
use Prokl\TwigExtensionsPackBundle\Services\Assets;
use Prokl\TwigExtensionsPackBundle\Services\Exceptions\WebpackUtilException;
use Prokl\TwigExtensionsPackBundle\Twig\Functions\SymfonyEncore;

/**
 * Class SymfonyEncoreTest
 * @package Tests\Twig
 * @coversDefaultClass SymfonyEncore
 *
 * @since 14.10.2020 Актуализация.
 */
class SymfonyEncoreTest extends BaseTestCase
{
    /**
     * @var SymfonyEncore $obTestObject Тестируемый объект.
     */
    protected $obTestObject;

    /**
     * entryLinkTag(). Успех.
     *
     * @return void
     */
    public function testEntryLinkTag() : void
    {
        $returnUrl = '/test/url.css';

        $this->obTestObject = new SymfonyEncore(
            $this->getMockAssetsService($returnUrl)
        );

        $result = $this->obTestObject->entryLinkTag('entry');

        $this->assertSame(
            '<link rel="stylesheet" href="' . $returnUrl . '">',
            $result,
            'Неправильная генерация ссылки.'
        );
    }

    /**
     * entryLinkTag(). Отработка link="preload".
     */
    public function testEntryLinkTagLinkPreload() : void
    {
        $returnUrl = '/test/url.css';

        $this->obTestObject = new SymfonyEncore(
            $this->getMockAssetsService($returnUrl)
        );

        $result = $this->obTestObject->entryLinkTag('entry', true);

        $this->assertSame(
            '<link rel="preload" as="style" href="'
            . $returnUrl . '"><link rel="stylesheet" href="'
            . $returnUrl . '">',
            $result,
            'Неправильная генерация ссылки.'
        );
    }

    /**
     * entryLinkTag(). Невалидная точка входа.
     */
    public function testEntryLinkTagInvalidEntrypoint() : void
    {
        $this->obTestObject = new SymfonyEncore(
            $this->getMockAssetsServiceFailEntryPoint()
        );

        $result = $this->obTestObject->entryLinkTag('entry');

        $this->assertEmpty(
            $result,
            'Неправильная генерация ссылки при невалидной точке входа.'
        );
    }

    /**
     * entryLinkScript(). Успех.
     */
    public function testEntryLinkScript() : void
    {
        $returnUrl = '/test/url.js';

        $this->obTestObject = new SymfonyEncore(
            $this->getMockAssetsService($returnUrl)
        );

        $result = $this->obTestObject->entryLinkScript('entry');

        $this->assertSame(
            '<script src="' . $returnUrl . '"></script>',
            $result,
            'Неправильная генерация ссылки.'
        );
    }

    /**
     * entryLinkScript(). Невалидная точка входа.
     */
    public function testEntryLinkScriptInvalidEntrypoint() : void
    {
        $this->obTestObject = new SymfonyEncore(
            $this->getMockAssetsServiceFailEntryPoint()
        );

        $result = $this->obTestObject->entryLinkScript('entry');

        $this->assertEmpty(
            $result,
            'Неправильная генерация ссылки при невалидной точке входа.'
        );
    }

    /**
     * Мок AssetsService.
     *
     * @param string $returnUrl
     *
     * @return mixed
     */
    private function getMockAssetsService(string $returnUrl)
    {
        return Mockery::mock(
            Assets::class
        )
            ->shouldReceive('getEntry')
            ->andReturn($returnUrl)
            ->once()
            ->getMock();
    }

    /**
     * Мок AssetsService. Невалидная точка входа.
     *
     * @return mixed
     */
    private function getMockAssetsServiceFailEntryPoint()
    {
        return Mockery::mock(
            Assets::class
        )
            ->shouldReceive('getEntry')
            ->andThrow(WebpackUtilException::class)
            ->once()
            ->getMock();
    }
}
