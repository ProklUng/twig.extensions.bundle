<?php

namespace Prokl\TwigExtensionsPackBundle;

use Prokl\TwigExtensionsPackBundle\DependencyInjection\TwigExtensionsPackExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class TwigExtensionsPackBundle
 * @package Prokl\TwigExtensionsPackBundle
 *
 * @since 22.04.2021
 */
class TwigExtensionsPackBundle extends Bundle
{
   /**
   * @inheritDoc
   */
    public function getContainerExtension()
    {
        if ($this->extension === null) {
            $this->extension = new TwigExtensionsPackExtension();
        }

        return $this->extension;
    }
}
