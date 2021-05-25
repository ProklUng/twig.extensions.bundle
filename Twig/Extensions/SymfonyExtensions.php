<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class SymfonyExtensions
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions
 *
 * @since 10.05.2021
 */
class SymfonyExtensions extends AbstractExtension
{
    /**
     * @var CsrfTokenManagerInterface $csrfManager CSRF manager.
     */
    private $csrfManager;

    /**
     * SymfonyExtensions constructor.
     *
     * @param CsrfTokenManagerInterface $csrfManager CSRF manager.
     */
    public function __construct(CsrfTokenManagerInterface $csrfManager)
    {
        $this->csrfManager = $csrfManager;
    }

    /**
     * Return extension name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'twig/symfony-extensions';
    }

    /**
     * Функции.
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('csrf_token', [$this->csrfManager, 'getToken']),
        ];
    }
}
