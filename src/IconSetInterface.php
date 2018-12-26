<?php

namespace Shapecode\Iconify;

/**
 * Interface IconSetInterface
 *
 * @package Shapecode\Iconify
 * @author  Nikita Loges
 */
interface IconSetInterface
{

    /**
     * @return string
     */
    public function getPrefix(): string;

    /**
     * @return array
     */
    public function getIcons(): array;

    /**
     * @param string $name
     *
     * @return array
     */
    public function getIconData(string $name): array;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function exists(string $name): bool;

    /**
     * @param bool $includeAliases
     *
     * @return array
     */
    public function listIcons(bool $includeAliases = false): array;

    /**
     * @param $name
     *
     * @return SVGInterface
     */
    public function getSVG($name): SVGInterface;

    /**
     * @param array $options
     *
     * @return string
     */
    public function scriptify(array $options = []): string;
}
