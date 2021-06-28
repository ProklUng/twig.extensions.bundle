<?php

namespace Prokl\TwigExtensionsPackBundle\Tests\Cases;

use Mockery;
use Prokl\TestingTools\Base\BaseTestCase;
use Prokl\TwigExtensionsPackBundle\Twig\Extensions\IncludeExtension;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class IncludeTest
 * @package Tests\Twig
 * @coversDefaultClass IncludeExtension
 *
 * @since 14.10.2020 Актуализация.
 */
class IncludeTest extends BaseTestCase
{
    /**
     * @var IncludeExtension $obTestObject Тестируемый объект.
     */
    protected $obTestObject;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->obTestObject = new IncludeExtension(
          $_SERVER['DOCUMENT_ROOT'],
          new Filesystem()
        );
    }

    /**
     * @return void
     */
    public function testIncludeNotFoundFile() : void
    {
        $result = $this->obTestObject->includeFile($this->faker->slug . '.php');

        $this->assertEmpty($result);
    }

    /**
     * @return void
     */
    public function testIncludeFile() : void
    {
        $result = $this->obTestObject->includeFile('/Tests/Fixtures/include_file.php');

        $this->assertSame('111<div>222</div>', $result);
    }

    /**
     * @return void
     */
    public function testIncludeFileHtml() : void
    {
        $result = $this->obTestObject->includeFile('/Tests/Fixtures/include.html');

        $this->assertSame('<div>html</div>', $result);
    }
}
