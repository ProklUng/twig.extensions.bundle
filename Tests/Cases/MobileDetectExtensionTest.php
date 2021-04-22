<?php

namespace Prokl\TwigExtensionsPackBundle\Tests\Cases;

use Prokl\TwigExtensionsPackBundle\Twig\Extensions\MobileDetectExtension;
use Mobile_Detect;
use Mockery;
use Prokl\TestingTools\Base\BaseTestCase;

/**
 * Class MobileDetectExtensionTest
 * @package Prokl\TwigExtensionsPackBundle\Tests\Cases
 * @coversDefaultClass MobileDetectExtension
 *
 * @since 12.10.2020
 */
class MobileDetectExtensionTest extends BaseTestCase
{
    /**
     * @var MobileDetectExtension $obTestObject Тестируемый объект.
     */
    protected $obTestObject;

    /**
    * isPhone().
    *
    * @return void
    */
    public function testIsPhone() : void
    {
        $this->obTestObject = new MobileDetectExtension(
            $this->getMockMobileDetect()
        );

        $result = $this->obTestObject->isPhone();

        $this->assertTrue(
            $result,
            'Определение мобилы неправильное.'
        );

        $this->obTestObject = new MobileDetectExtension(
            $this->getMockMobileDetect(false)
        );

        $result = $this->obTestObject->isPhone();

        $this->assertFalse(
            $result,
            'Определение десктопа неправильное.'
        );
    }


    /**
     * Проксирование вызовов.
     *
     * @return void
     */
    public function testProxy() : void
    {
        $this->obTestObject = new MobileDetectExtension(
            $this->getMockMobileDetect()
        );

        $result = $this->obTestObject->isMobile();

        $this->assertTrue(
            $result,
            'Определение мобилы неправильное.'
        );
    }

    /**
     * Мок Mobile_Detect.
     *
     * @param boolean $isMobile
     * @param boolean $isTablet
     *
     * @return mixed
     */
    private function getMockMobileDetect(
        bool $isMobile = true,
        bool $isTablet = false
    ) {
        return Mockery::mock(
            Mobile_Detect::class
        )
            ->makePartial()
            ->shouldReceive(
                [
                    'isMobile' => $isMobile,
                    'isTablet' => $isTablet,
                ]
            )
            ->getMock();
    }
}
