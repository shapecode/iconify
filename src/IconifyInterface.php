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
     * @param null $dir
     *
     * @return IconSet
     */
    public function getSet(string $lib, ?string $dir = null): IconSetInterface;

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
     *
     * @return IconSetInterface
     */
    public function loadSet(string $lib, ?string $dir = null): IconSetInterface;

    /**
     * @param string      $icon
     * @param string|null $dir
     *
     * @return SVGInterface
     */
    public function getSVG(string $icon, ?string $dir = null): SVGInterface;
}
