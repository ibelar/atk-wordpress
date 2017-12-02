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

use atkwp\helpers\WpUtil;
use atkwp\interfaces\ComponentCtrlInterface;
use atkwp\services\MetaBoxService;
use atkwp\services\PanelService;
use atkwp\services\EnqueueService;

class ComponentController implements ComponentCtrlInterface
{
    //Components use by the plugin
    public $components = [];
    public $componentType = ['panel', 'metaBox', 'shortcode', 'widget'];
    public $componentServices = [];

    public function __construct() {}

    public function initializeComponents($plugin)
    {
        $assetUrl = WpUtil::getPluginUrl('assets', $plugin->pathFinder->getAssetsPath());
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
    }

    public function registerComponents($type, $components)
    {
        $this->components[$type] = $components;
    }



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
        return null;
    }

    public function getPostMetaData($postID, $postKey, $single = true)
    {
        return $this->componentServices['metaBoxes']->getPostMetaData($postID, $postKey, $single);
    }

    public function savePostMetaData( $postID, $postKey, $postValue )
    {
        $this->componentServices['metaBoxes']->savePostMetaData($postID, $postKey, $postValue);
    }
}