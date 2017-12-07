<?php
/**
 * Initialize all WP services needed to run the Plugin.
 * Services will set specific WP hook up functions to run a specific component in WP like:
 *  - Panel,
 *  - MetaBox,
 *  - Shortcode,
 *  - Widgets
 * Also Keep track of how many component each service is loading.
 */

namespace atkwp\controllers;

use atkwp\AtkWp;
use atkwp\helpers\WpUtil;
use atkwp\interfaces\ComponentCtrlInterface;
use atkwp\services\EnqueueService;
use atkwp\services\MetaBoxService;
use atkwp\services\PanelService;
use atkwp\services\WidgetService;

class ComponentController implements ComponentCtrlInterface
{
    //Components use by the plugin
    public $components = [];
    public $componentType = ['panel', 'metaBox', 'shortcode', 'widget'];
    public $componentServices = [];

    public function __construct()
    {
    }

    /**
     * Implementation of WP services.
     * Each service wired together WP action hook or function in order to
     * create their corresponding components base on configuration setup.
     *
     * @param AtkWp $plugin
     */
    public function initializeComponents(AtkWp $plugin)
    {
        $assetUrl = WpUtil::getPluginUrl('assets', $plugin->pathFinder->getAssetsPath());
        $this->plugin = $plugin;

        $this->componentServices['enqueue'] = new EnqueueService(
            $this,
            $plugin->config->getConfig('enqueue', []),
            $assetUrl
        );

        $this->componentServices['panel'] = new PanelService(
            $this,
            $plugin->config->getConfig('panel', []),
            [$plugin, 'wpAdminExecute'],
            $assetUrl
        );

        $this->componentServices['metaBoxes'] = new MetaBoxService(
            $this,
            $plugin->config->getConfig('metabox', []),
            [$plugin, 'wpMetaBoxExecute']
        );

        $this->componentServices['widget'] = new WidgetService(
            $this,
            $plugin->config->getConfig('widget', []),
            $plugin
        );
    }

    /**
     * Add components created by services to the list of components.
     *
     * @param string $type       The component type.
     * @param array  $components The array containing all components of this type.
     */
    public function registerComponents($type, array $components)
    {
        $this->components[$type] = $components;
    }

    /**
     * Get a component using it's type and a key - value.
     *
     * @param string $type
     * @param string $search
     * @param string $searchKey
     *
     * @return array|null
     */
    public function getComponentByType($type, $search, $searchKey = 'id')
    {
        $comp = null;
        foreach ($this->components as $key => $component) {
            if ($key === $type) {
                foreach ($this->components[$type] as $componentType) {
                    if ($componentType[$searchKey] === $search) {
                        $comp = $componentType;
                    }
                }
            }
        }

        return $comp;
    }

    /**
     * Return a component from the components array.
     *
     * @param string $search
     * @param array  $components
     *
     * @return array|mixed
     */
    public function getComponentByKey($search, $components = [])
    {
        if (empty($components)) {
            $components = $this->components;
        }
        foreach ($components as $key => $subComponents) {
            if ($key === $search) {
                return $subComponents;
            }
            if (in_array($key, $this->componentType)) {
                return $this->getComponentByKey($search, $subComponents);
            }
        }
    }

    /**
     * Get meta data value associated to a post.
     *
     * @param int    $postID
     * @param string $postKey
     * @param bool   $single
     *
     * @return mixed
     */
    public function getPostMetaData($postID, $postKey, $single = true)
    {
        return $this->componentServices['metaBoxes']->getPostMetaData($postID, $postKey, $single);
    }

    /**
     * Save meta data associated to a post.
     *
     * @param int    $postID
     * @param string $postKey
     * @param mixed  $postValue
     *
     * @return mixed
     */
    public function savePostMetaData($postID, $postKey, $postValue)
    {
        return $this->componentServices['metaBoxes']->savePostMetaData($postID, $postKey, $postValue);
    }
}
