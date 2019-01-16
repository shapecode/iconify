<?php

namespace Shapecode\Iconify;

/**
 * Interface IconifyInterface
 *
 * @package Shapecode\Iconify
 * @author  Nikita Loges
 */
interface IconifyInterface
{

    /**
     * @param      $lib
     *
     * @return IconSet
     */
    public function getSet(string $lib): IconSetInterface;

    /**
     * @param string $lib
     *
     * @return bool
     */
    public function hasSet(string $lib): bool;

    /**
     * @param string           $lib
     * @param IconSetInterface $set
     */
    public function addSet(string $lib, IconSetInterface $set): void;

    /**
     * @param string      $lib
     * @param string|null $dir
     */
    public function loadSet(string $lib, ?string $dir = null): void;

    /**
     * @param string $icon
     *
     * @return SVGInterface
     */
    public function getSVG(string $icon): SVGInterface;

    /**
     * @param string $icon
     * @param array  $options
     *
     * @return string
     */
    public function getBody(string $icon, array $options = []): string;
}
