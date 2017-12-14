<?php
/* =====================================================================
 * atk-wordpress => Wordpress interface for Agile Toolkit Framework.
 *
 * This interface enable the use of the Agile Toolkit framework within a WordPress site.
 *
 * Please note that when atk is mentioned it generally refer to Agile Toolkit.
 * More information on Agile Toolkit: http://www.agiletoolkit.org
 *
 * Author: Alain Belair
 * https://github.com/ibelar
 * Licensed under MIT
 * =====================================================================*/
/**
 * Simple configuration utilities.
 */

namespace atkwp\helpers;

class Config
{
    const UNDEFINED = '_atk4_undefined_value';

    /**
     * Contains all configuration options.
     *
     * @var array
     */
    public $config;

    /**
     * Contains the path to the configuration files.
     *
     * @var string
     */
    public $configPath;

    /**
     * Default configuration files to read.
     *
     * @var array
     */
    public $wpConfigFiles = [
        'config-default',
        'config-wp',
        'config-panel',
        'config-enqueue',
        'config-shortcode',
        'config-widget',
        'config-metabox',
        'config-dashboard',
        ];

    /**
     * Config constructor.
     *
     * @param string $configPath The path to configuration files.
     */
    public function __construct($configPath)
    {
        $this->configPath = $configPath;
        $this->config = $this->loadConfiguration();
    }

    /**
     * Manually set configuration option.
     *
     * @param array $config
     * @param mixed $val
     *
     * @return mixed
     */
    public function setConfig($config = [], $val = self::UNDEFINED)
    {
        if ($val !== self::UNDEFINED) {
            return $this->setConfig([$config => $val]);
        }
        $this->config = array_merge($this->config ?: [], $config ?: []);
    }

    /**
     * Return a configuration value,
     * or a default value if no configuration is found and a default value is supply,
     * or null if no configuration is found and no default value is supply.
     *
     * @param string $path
     * @param mixed  $default_value
     *
     * @return mixed||null
     */
    public function getConfig($path, $default_value = self::UNDEFINED)
    {
        $parts = explode('/', $path);
        $current_position = $this->config;
        foreach ($parts as $part) {
            if (!array_key_exists($part, $current_position)) {
                if ($default_value !== self::UNDEFINED) {
                    return $default_value;
                }

                return;
            } else {
                $current_position = $current_position[$part];
            }
        }

        return $current_position;
    }

    /**
     * Load configuration files.
     *
     * @return array
     */
    private function loadConfiguration()
    {
        $loadedConfig = [];
        foreach ($this->wpConfigFiles as $fileName) {
            $config = [];
            if (strpos($fileName, '.php') != strlen($fileName) - 4) {
                $fileName .= '.php';
            }
            $filePath = $this->configPath.'/'.$fileName;

            if (file_exists($filePath)) {
                include $filePath;
            }
            $loadedConfig = array_merge($loadedConfig, $config);
        }

        return $loadedConfig;
    }
}
