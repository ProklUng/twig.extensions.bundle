<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions\Examples;

use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ExampleServiceForRender
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions\Examples
 *
 * @since 18.01.2021
 */
class ExampleServiceForRender
{
    public function action(int $id, Filesystem $filesystem): Response
    {
        return new Response(
            'OK'
        );
    }
}
