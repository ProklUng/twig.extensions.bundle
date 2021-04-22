<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use Mobile_Detect;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class MobileDetectExtension
 * MobileDetect extension.
 * @package Local\Services\Twig\Extensions
 *
 * @since 11.10.2020.
 * @since 24.10.2020 Обновление интерфейса. Убран deprecated Twig_ExtensionInterface.
 * @since 03.11.2020 Чистка.
 */
class MobileDetectExtension extends AbstractExtension
{
    /**
     * @var Mobile_Detect $detector Детектор мобил.
     */
    private $detector;

    /**
     * MobileDetectExtension constructor.
     *
     * @param Mobile_Detect $mobileDetect
     */
    public function __construct(Mobile_Detect $mobileDetect)
    {
        $this->detector = $mobileDetect;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'twig/mobiledetect';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('is_mobile', [$this, 'isMobile']),
            new TwigFunction('is_tablet', [$this, 'isTablet']),
            new TwigFunction('is_phone', [$this, 'isPhone'])
        ];
    }

    /**
     * Это мобильник?
     *
     * @return boolean
     */
    public function isPhone() : bool
    {
        return $this->detector->isMobile()
            &&
            !$this->detector->isTablet();
    }

    /**
     * Pass through calls of undefined methods to the mobile detect library.
     *
     * @param string $name      Метод.
     * @param mixed  $arguments Аргументы.
     *
     * @return mixed
     */
    public function __call(string $name, $arguments)
    {
        return call_user_func_array([$this->detector, $name], $arguments);
    }
}
