<?php

namespace Shapecode\Iconify;

/**
 * Interface SVGInterface
 *
 * @package Shapecode\Iconify
 * @author  Nikita Loges
 */
interface SVGInterface
{

    /**
     * @param array $props
     *
     * @return string
     */
    public function getSVG($props = []);
}
