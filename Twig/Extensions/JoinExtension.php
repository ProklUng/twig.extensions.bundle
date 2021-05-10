<?php

namespace Prokl\TwigExtensionsPackBundle\Twig\Extensions;

use Traversable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class JoinExtension
 * @package Prokl\TwigExtensionsPackBundle\Twig\Extensions
 *
 * @since 26.02.2021
 */
class JoinExtension extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'twig/join-extension';
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('naturaljoin', [$this, 'join']),
        ];
    }

    /**
     * @param array|Traversable $array
     * @param string            $seperator
     *
     * @return string
     */
    public function join($array, string $seperator): string
    {
        if ($array instanceof Traversable) {
            $array = iterator_to_array($array, false);
        }

        $result = [];
        $numeric = true;

        foreach ($array as $key => $item) {
            if (is_numeric($key)) {
                $result[] = "'".$item."'";
                continue;
            }

            $numeric = false;
            $result[$key] = "'".$item."'";
        }

        return $numeric ? implode($seperator, $result)
                    : implode($seperator, $this->arrayMapAssoc($result));
    }

    /**
     * @param array $array
     *
     * @return array
     */
    private function arrayMapAssoc(array $array) : array
    {
        $r = [];
        foreach ($array as $key => $value) {
            $r[$key] = "'$key' => $value";
        }

        return $r;
    }
}
