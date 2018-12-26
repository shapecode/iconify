<?php

namespace Shapecode\Iconify;

/**
 * Interface IconSetLoaderInterface
 *
 * @package Shapecode\Iconify
 * @author  Nikita Loges
 */
interface IconSetLoaderInterface
{

    /**
     * @param      $name
     * @param null $dir
     *
     * @return IconSet
     */
    public function loadSet(string $name, ?string $dir = null): IconSet;
}
