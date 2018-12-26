<?php

namespace Shapecode\Iconify;

use Iconify\IconsJSON\Finder;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class IconSetLoader
 *
 * @package Shapecode\Iconify
 * @author  Nikita Loges
 */
class IconSetLoader implements IconSetLoaderInterface
{

    /** @var CacheItemPoolInterface */
    protected $cache;

    /**
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritdoc
     */
    public function loadSet(string $name, ?string $dir = null): IconSet
    {
        $key = $this->getCacheKey($name);
        $timeKey = $key . '-mtime';

        $cacheItem = $this->cache->getItem($key);
        $cacheTime = $this->cache->getItem($timeKey);

        $filename = $this->findSet($name, $dir);
        $mtime = filemtime($filename);

        if ($cacheItem->isHit() && $cacheTime->isHit() && $cacheTime->get() === $mtime) {
            $setData = $cacheItem->get();

            return new IconSet($setData);
        }

        $setData = $this->loadFile($filename);

        $cacheItem->set($setData);
        $cacheTime->set($mtime);

        $cacheItem->expiresAfter(new \DateInterval('P1Y'));
        $cacheTime->expiresAfter(new \DateInterval('P1Y'));

        $this->cache->save($cacheItem);
        $this->cache->save($cacheTime);

        return new IconSet($setData);
    }

    /**
     * @param      $name
     * @param null $dir
     *
     * @return string
     */
    protected function findSet(string $name, ?string $dir = null): string
    {
        if ($dir === null) {
            $dir = Finder::rootDir();
        }

        return $dir . '/json/' . $name . '.json';
    }

    /**
     * @param string $filename
     *
     * @return array
     */
    protected function loadFile(string $filename): array
    {
        // Load from file
        $data = file_get_contents($filename);

        return $this->loadJSON($data);
    }

    /**
     * @param string $data
     *
     * @return array
     */
    protected function loadJSON(string $data): array
    {
        $data = json_decode($data, true);

        // Validate
        if (!isset($data['icons'], $data['prefix'])) {
            return [];
        }

        return $data;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getCacheKey(string $name): string
    {
        return 'iconify-' . $name;
    }
}
