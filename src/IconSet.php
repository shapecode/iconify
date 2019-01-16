<?php

namespace Shapecode\Iconify;

/**
 * Class IconSet
 *
 * @package Shapecode\Iconify
 * @author  Nikita Loges
 */
class IconSet implements IconSetInterface
{

    /** @var array */
    public $data;

    /** @var array */
    protected $_result;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function getPrefix(): string
    {
        return $this->data['prefix'];
    }

    /**
     * @param array $json
     * @param array $props
     *
     * @return array
     */
    public function optimize(array $json, array $props = []): array
    {
        if (empty($props)) {
            $props = ['width', 'height', 'top', 'left', 'inlineHeight', 'inlineTop', 'verticalAlign'];
        }

        // Delete empty aliases list
        if (isset($json['aliases']) && empty($json['aliases'])) {
            unset ($json['aliases']);
        }

        // Check all attributes
        foreach ($props as $prop) {
            $maxCount = 0;
            $maxValue = false;
            $counters = [];
            $failed = false;

            foreach ($json['icons'] as $key => $item) {
                if (!isset($item[$prop])) {
                    $failed = true;
                    break;
                }

                $value = $item[$prop];
                $valueKey = '' . $value;

                if (!$maxCount) {
                    // First item
                    $maxCount = 1;
                    $maxValue = $value;
                    $counters[$valueKey] = 1;
                    continue;
                }

                if (!isset($counters[$valueKey])) {
                    // First entry for new value
                    $counters[$valueKey] = 1;
                    continue;
                }

                $counters[$valueKey]++;

                if ($counters[$valueKey] > $maxCount) {
                    $maxCount = $counters[$valueKey];
                    $maxValue = $value;
                }
            }

            if (!$failed && $maxCount > 1) {
                // Remove duplicate values
                $json[$prop] = $maxValue;
                foreach ($json['icons'] as $key => $item) {
                    if ($item[$prop] === $maxValue) {
                        unset($json['icons'][$key][$prop]);
                    }
                }
            }
        }

        return $json;
    }

    /**
     * @inheritdoc
     */
    public function getIcons(): array
    {
        return $this->data['icons'];
    }

    /**
     * @inheritdoc
     */
    public function getIconData(string $name): array
    {
        if (isset($this->data['icons'][$name])) {
            $data = $this->data['icons'][$name];
            $data = $this->addDefaultValues($data);

            return $this->addMissingAttributes($data);
        }

        // Alias
        if (!isset($this->data['aliases'][$name])) {
            return [];
        }

        $result = $this->data['aliases'][$name];
        $parent = $result['parent'];
        $iteration = 0;

        while ($iteration < 5) {
            if (isset($this->data['icons'][$parent])) {
                // Merge with icon
                $icon = $this->data['icons'][$parent];
                $icon = $this->addDefaultValues($icon);
                $result = $this->mergeIcon($result, $icon);

                return $this->addMissingAttributes($result);
            }

            if (!isset($this->data['aliases'][$parent])) {
                return [];
            }
            $result = $this->mergeIcon($result, $this->data['aliases'][$parent]);
            $parent = $this->data['aliases'][$parent]['parent'];
            $iteration++;
        }

        return [];
    }

    /**
     * @inheritdoc
     */
    public function exists(string $name): bool
    {
        return isset($this->data['icons'][$name]) || isset($this->data['aliases'][$name]);
    }

    /**
     * @inheritdoc
     */
    public function listIcons(bool $includeAliases = false): array
    {
        $result = array_keys($this->data['icons']);

        if ($includeAliases && isset($this->data['aliases'])) {
            $result = array_merge($result, array_keys($this->data['aliases']));
        }

        return $result;
    }

    /**
     * @param $name
     *
     * @return SVGInterface
     */
    public function getSVG($name): SVGInterface
    {
        $iconData = $this->getIconData($name);

        if (empty($iconData)) {
            throw new \RuntimeException('icon ' . $name . ' not found');
        }

        return new SVG($iconData);
    }

    /**
     * @inheritdoc
     */
    public function scriptify(array $options = []): string
    {
        $defaultOptions = [
            // Array of icons to get
            'icons'    => null,

            // JavaScript callback function. Default callback uses SimpleSVG instead of Iconify for backwards compatibility
            // with Iconify 1.0.0-beta6 (that used to be called SimpleSVG) and older versions.
            'callback' => 'SimpleSVG.addCollection',

            // True if result should be optimized for smaller file size
            'optimize' => false,

            // True if result should be pretty for easy reading
            'pretty'   => false
        ];

        $options = array_merge($options, $defaultOptions);

        // Get JSON data
        $json = $this->getIcons();

        if ($options['optimize']) {
            $json = $this->optimize($json);
        }

        $json = json_encode($json, $options['pretty'] ? JSON_PRETTY_PRINT : 0);

        // Wrap in callback
        return $options['callback'] . '(' . $json . ");\n";
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function addMissingAttributes(array $data): array
    {
        $item = array_merge([
            'left'   => 0,
            'top'    => 0,
            'width'  => 16,
            'height' => 16,
            'rotate' => 0,
            'hFlip'  => false,
            'vFlip'  => false
        ], $data);
        if (!isset($item['inlineTop'])) {
            $item['inlineTop'] = $item['top'];
        }
        if (!isset($item['inlineHeight'])) {
            $item['inlineHeight'] = $item['height'];
        }
        if (!isset($item['verticalAlign'])) {
            // -0.143 if icon is designed for 14px height,
            // otherwise assume icon is designed for 16px height
            $item['verticalAlign'] = $item['height'] % 7 === 0 && $item['height'] % 8 !== 0 ? -0.143 : -0.125;
        }

        return $item;
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    protected function addDefaultValues(array $data): array
    {
        foreach ($this->data as $key => $value) {
            if (!isset($data[$key]) && (is_numeric($value) || is_bool($value))) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * @param array $result
     * @param array $data
     *
     * @return array
     */
    protected function mergeIcon(array $result, array $data): array
    {
        foreach ($data as $key => $value) {
            if (!isset($result[$key])) {
                $result[$key] = $value;
                continue;
            }
            switch ($key) {
                case 'rotate':
                    $result['rotate'] += $value;
                    break;

                case 'hFlip':
                case 'vFlip':
                    $result[$key] = $result[$key] !== $value;
            }
        }

        return $result;
    }
}
