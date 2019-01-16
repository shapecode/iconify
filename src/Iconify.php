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
    public function getSet(string $lib): IconSetInterface
    {
        if (!$this->hasSet($lib)) {
            $this->loadSet($lib);
        }

        if (!$this->hasSet($lib)) {
            throw new \RuntimeException('set ' . $lib . ' not found');
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
    public function loadSet(string $lib, ?string $dir = null): void
    {
        $set = $this->loader->loadSet($lib, $dir);

        if ($set !== null) {
            $this->addSet($lib, $set);
        }
    }

    /**
     * @inheritdoc
     */
    public function getSVG(string $icon): SVGInterface
    {
        [$lib, $name] = explode(':', $icon);

        try {
            $set = $this->getSet($lib);

            return $set->getSVG($name);
        } catch (\Exception $e) {
            $set = $this->getSet('fa-solid');

            return $set->getSVG('question-circle');
        }
    }

    /**
     * @inheritdoc
     */
    public function getBody(string $icon, array $options = []): string
    {
        return $this->getSVG($icon)->getSVG($options);
    }
}
