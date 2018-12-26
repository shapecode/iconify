<?php

namespace Shapecode\Iconify;

/**
 * Class Iconify
 *
 * @package Shapecode\Iconify
 * @author  Nikita Loges
 */
class Iconify implements IconifyInterface
{

    /** @var IconSet[] */
    protected $sets;

    /** @var IconSetLoader */
    protected $loader;

    /**
     * @param IconSetLoader $loader
     */
    public function __construct(IconSetLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @inheritdoc
     */
    public function getSet(string $lib, ?string $dir = null): IconSetInterface
    {
        if (!$this->hasSet($lib)) {
            $set = $this->loadSet($lib, $dir);

            $this->addSet($lib, $set);
        }

        return $this->sets[$lib];
    }

    /**
     * @inheritdoc
     */
    public function hasSet(string $lib): bool
    {
        return isset($this->sets[$lib]);
    }

    /**
     * @inheritdoc
     */
    public function addSet(string $lib, IconSetInterface $set): void
    {
        if (!$this->hasSet($lib)) {
            $this->sets[$lib] = $set;
        }
    }

    /**
     * @inheritdoc
     */
    public function loadSet(string $lib, ?string $dir = null): IconSetInterface
    {
        return $this->loader->loadSet($lib, $dir);
    }

    /**
     * @inheritdoc
     */
    public function getSVG(string $icon, ?string $dir = null): SVGInterface
    {
        list($lib, $name) = explode(':', $icon);

        $set = $this->getSet($lib, $dir);

        return $set->getSVG($name);
    }
}
