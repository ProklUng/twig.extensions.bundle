<?php

namespace Prokl\TwigExtensionsPackBundle\Services\Handlers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class AppVariable
 * @package Prokl\TwigExtensionsPackBundle\Services
 *
 * @since 13.09.2020 Отвязал от BaseServiceClass. Зависимости снаружи.
 * @since 28.10.2020 Change to SessionInterface.
 */
class AppVariable
{
    /**
     * @var SessionInterface $sessions Сессии Symfony.
     */
    private $sessions;

    /**
     * @var Request $appRequest Request приложения.
     */
    private $appRequest;

    /**
     * AppVariable constructor.
     *
     * @param SessionInterface $sessions   Symfony Session.
     * @param Request          $appRequest Глобальный Request приложения.
     */
    public function __construct(
        SessionInterface $sessions,
        Request $appRequest
    ) {
        $this->sessions = $sessions;
        $this->appRequest = $appRequest;
    }

    /**
     * Получить массив для подмеса в твиговский контекст.
     *
     * @return array
     */
    public function get(): array
    {
        return [
            'app' => [
                'wp.debug' => $_ENV['WP_DEBUG'] !== 'false',
                'debug' => $_ENV['APP_DEBUG'] !== 'false',
                'environment' => $_ENV['APP_ENV'],
                'secret' => $_ENV['APP_SECRET'],
                'session' => $this->sessions,
                'request' => $this->appRequest,
            ],
        ];
    }
}
