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
 * Licensed under MIT
 * =====================================================================*/
/**
 * Simple configuration utilities.
 */

namespace atkwp\helpers;

class Config
{
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
    public function setConfig($config = [], $val = UNDEFINED)
    {
        if ($val !== UNDEFINED) {
            return $this->setConfig([$config => $val]);
        }
        $this->config = array_merge($this->config ?: [], $config ?: []);
    }

    /**
     * Load config if necessary and look up corresponding setting.
     *
     * @param string $path
     * @param mixed  $default_value
     *
     * @return string
     */
    public function getConfig($path, $default_value = UNDEFINED)
    {
        /*
         * For given path such as 'dsn' or 'logger/log_dir' returns
         * corresponding config value. Throws ExceptionNotConfigured if not set.
         *
         * To find out if config is set, do this:
         *
         * $var_is_set = true;
         * try { $app->getConfig($path); } catch ExceptionNotConfigured($e) { $var_is_set=false; }
         */
        $parts = explode('/', $path);
        $current_position = $this->config;
        foreach ($parts as $part) {
            if (!array_key_exists($part, $current_position)) {
                if ($default_value !== UNDEFINED) {
                    return $default_value;
                }

                throw $this->exception('Configuration parameter is missing in config.php', 'NotConfigured')
                           ->addMoreInfo('config_files_loaded', $this->config_files_loaded)
                           ->addMoreInfo('missign_line', " \$config['".implode("']['", explode('/', $path))."']");
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
