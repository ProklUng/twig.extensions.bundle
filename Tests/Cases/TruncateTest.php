<?php

namespace Prokl\TwigExtensionsPackBundle\Tests\Cases;

use Prokl\TestingTools\Base\BaseTestCase;
use Prokl\TwigExtensionsPackBundle\Twig\Extensions\StringTwigExtension;

/**
 * Class TruncateTest
 * @package Tests\Twig
 * @coversDefaultClass StringTwigExtension
 *
 * @since 28.06.2021.
 */
class TruncateTest extends BaseTestCase
{
    /**
     * @var StringTwigExtension $obTestObject Тестируемый объект.
     */
    protected $obTestObject;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->obTestObject = new StringTwigExtension();
    }

    /**
     * @dataProvider dataProvider
     *
     * @param $text
     * @param $length
     * @param $ending
     * @param $exact
     * @param $considerHtml
     * @param $expected
     *
     * @return void
     */
    public function testTruncate($text, $length, $ending, $exact, $considerHtml, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->obTestObject->truncate($text, $length, $ending, $exact, $considerHtml)
        );
    }

    /**
     * @return array[]
     */
    public function dataProvider() : array
    {
        return [
            ['Fourscore and seven years ago', 9, '', true, false, 'Fourscore'],
            ['Fourscore and seven years ago', 9, '', false, false, 'Fourscore'],
            ['Fourscore and seven years ago', 10, '', true, false, 'Fourscore '],
            ['Fourscore and seven years ago', 10, '', false, false, 'Fourscore'],
            ['Fourscore and seven years ago', 11, '', true, false, 'Fourscore a'],
            ['Fourscore and seven years ago', 11, '', false, false, 'Fourscore'],
            ['Fourscore and seven years ago', 9, '...', true, false, 'Foursc...'],
            ['Fourscore and seven years ago', 9, '...', false, false, 'Foursc...'],
            ['Fourscore and seven years ago', 14, '...', true, false, 'Fourscore a...'],
            ['Fourscore and seven years ago', 14, '...', false, false, 'Fourscore...'],
            ['Fourscore and seven years ago', 16, '...', true, false, 'Fourscore and...'],
            ['Fourscore and seven years ago', 16, '...', false, false, 'Fourscore...'],
            ['Fourscore and seven years ago', 18, '...', true, false, 'Fourscore and s...'],
            ['Fourscore and seven years ago', 18, '...', false, false, 'Fourscore and...'],
            ['<div>Fourscore and seven years ago</div>', 9, '', true, true, '<div>Fourscore</div>'],
            ['<div>Fourscore and seven years ago</div>', 9, '', false, true, '<div>Fourscore</div>'],
            ['<div>Fourscore and seven years ago</div>', 10, '', true, true, '<div>Fourscore </div>'],
            ['<div>Fourscore and seven years ago</div>', 10, '', false, true, '<div>Fourscore</div>'],
            ['<div>Fourscore and seven years ago</div>', 11, '', true, true, '<div>Fourscore a</div>'],
            ['<div>Fourscore and seven years ago</div>', 11, '', false, true, '<div>Fourscore</div>'],
            ['<div>Fourscore and seven years ago</div>', 9, '...', true, true, '<div>Foursc</div>...'],
            ['<div>Fourscore and seven years ago</div>', 9, '...', false, true, '<div>Foursc</div>...'],
            ['<div>Fourscore and seven years ago</div>', 14, '...', true, true, '<div>Fourscore a</div>...'],
            ['<div>Fourscore and seven years ago</div>', 14, '...', false, true, '<div>Fourscore</div>...'],
            ['<div>Fourscore and seven years ago</div>', 16, '...', true, true, '<div>Fourscore and</div>...'],
            ['<div>Fourscore and seven years ago</div>', 16, '...', false, true, '<div>Fourscore</div>...'],
            ['<div>Fourscore and seven years ago</div>', 18, '...', true, true, '<div>Fourscore and s</div>...'],
            ['<div>Fourscore and seven years ago</div>', 18, '...', false, true, '<div>Fourscore and</div>...'],
            ['<div style="color:red">Fourscore and seven years ago</div>', 9, '', true, true, '<div style="color:red">Fourscore</div>'],
            ['<div style="color:red">Fourscore and seven years ago</div>', 9, '', false, true, '<div style="color:red">Fourscore</div>'],
            ['žš1čř£^_{}|}~žščř"', 4, '', true, false, 'žš1č'],
            ['žš1čř£^_{}|}~žščř"', 4, '', false, false, 'žš1č'],
        ];
    }
}
