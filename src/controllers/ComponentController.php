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
use atkwp\services\DashboardService;
use atkwp\services\EnqueueService;
use atkwp\services\MetaBoxService;
use atkwp\services\PanelService;
use atkwp\services\ShortcodeService;
use atkwp\services\WidgetService;

class ComponentController implements ComponentCtrlInterface
{
    /**
     * The components sets by Plugin and loaded via configuration file.
     *
     * @var array
     */
    public $components = [];

    /**
     * The component type supported.
     *
     * @var array
     */
    public $componentType = ['panel', 'metaBox', 'shortcode', 'widget', 'dashboard'];

    /**
     * The service that each component required.
     * Each service is responsible for setting proper action hook
     * and method for each component to work.
     *
     * @var array
     */
    public $componentServices = [];

    /**
     * ComponentController constructor.
     */
    public function __construct()
    {
    }

    /**
     * Initialisation of component services.
     * Each service wired together WP action hook or function in order to
     * create their enable corresponding components to run under Wp.
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

        $this->componentServices['dashboard'] = new DashboardService(
            $this,
            $plugin->config->getConfig('dashboard', []),
            [$plugin, 'wpDashboardExecute']
        );

        $this->componentServices['widget'] = new WidgetService(
            $this,
            $plugin->config->getConfig('widget', []),
            $plugin
        );

        $this->componentServices['shortcode'] = new ShortcodeService(
            $this,
            $plugin->config->getConfig('shortcode', []),
            [$plugin, 'wpShortcodeExecute']
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
     * Return components base on it's type.
     *
     * @param $type
     *
     * @return mixed
     */
    public function getComponentsByType($type)
    {
        return $this->components[$type];
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
    public function searchComponentByType($type, $search, $searchKey = 'id')
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
     * Return a component from the components array
     * base on it's key value regardless of the component type.
     *
     * @param string $search
     * @param array  $components
     *
     * @return array|mixed
     */
    public function searchComponentByKey($search, $components = [])
    {
        if (empty($components)) {
            $components = $this->components;
        }
        foreach ($components as $key => $subComponents) {
            if ($key === $search) {
                return $subComponents;
            }
            if (in_array($key, $this->componentType)) {
                if ($component = $this->searchComponentByKey($search, $subComponents)) {
                    return $component;
                }
            }
        }
    }

    /**
     * Enqueue shortcode js and css files for a particular shortcode.
     *
     * @param array $shortcode
     *
     * @return mixed|void
     */
    public function enqueueShortcodeFiles(array $shortcode)
    {
        $this->componentServices['enqueue']->enqueueShortCodeFiles($shortcode);
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
