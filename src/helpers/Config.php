<?php
/**
 * Created by abelair.
 * Date: 2017-11-24
 * Time: 10:48 AM
 */

namespace atkwp\helpers;


use atkwp\interfaces\PathInterface;

class Config
{
    public $config;
    public $configPath;

    //default config files to read
    public $wpConfigFiles = [
        'config-default',
        'config-wp',
        'config-panel',
        'config-enqueue',
        'config-shortcode',
        'config-widget',
        'config-metabox',
        'config-dashboard'];

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
     */
    public function setConfig($config = [], $val = UNDEFINED)
    {
        if ($val !== UNDEFINED) {
            return $this->setConfig(array($config => $val));
        }
        $this->config = array_merge($this->config ?: array(), $config ?: array());
    }

    /**
     * Load config if necessary and look up corresponding setting.
     *
     * @param string $path
     * @param mixed $default_value
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

    private function loadConfiguration() {
        $loadedConfig = [];
        foreach ($this->wpConfigFiles as $fileName) {
            $config = [];
            if (strpos( $fileName, '.php') != strlen($fileName) - 4) {
                $fileName .= '.php';
            }
            $filePath = $this->configPath . '/' . $fileName;

            if (file_exists($filePath)) {
                include $filePath;
            }
            $loadedConfig = array_merge($loadedConfig, $config);
        }
        return $loadedConfig;
    }
}