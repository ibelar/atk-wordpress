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
 * The component controller interface.
 */

namespace atkwp\interfaces;

use atkwp\AtkWp;

interface ComponentCtrlInterface
{
    /**
     * Create and initialized all Wordpress service, i.e.
     * hook action function needed for creating wordpress components.
     *
     * @param AtkWp $plugin
     */
    public function initializeComponents(AtkWp $plugin);

    /**
     * Add component by it's type to a component container.
     *
     * @param string $type
     * @param array  $components
     */
    public function registerComponents($type, array $components);

    /**
     * Get components by it's type.
     *
     * @param $type
     *
     * @return mixed
     */
    public function getComponentsByType($type);

    /**
     * Search a component by type from the component container.
     *
     * @param string $type      The type of component to search.
     * @param string $search    The value to search for.
     * @param string $searchKey The key use to search, default to id.
     *
     * @return array||null The component definition.
     */
    public function searchComponentByType($type, $search, $searchKey = 'id');

    /**
     * Search component by a key value in a components container.
     *
     * @param string $search     The value of the key to search for.
     * @param array  $components The component containers.
     *
     * @return array||null The component definition.
     */
    public function searchComponentByKey($search, $components);

    /**
     * Enqueue js and css files to use with a Wp shortcode.
     *
     * @param array $shortcode The shortcode configuration as set in shortcode-config.php.
     *
     * @return mixed
     */
    public function enqueueShortcodeFiles(array $shortcode);

    /**
     * Get meta data attach to a post.
     *
     * @param int    $postId The post id.
     * @param string $key    The meta key set in db to retrieve value for.
     * @param bool   $single Return single or multiple value.
     *
     * @return mixed Will be an array if single is false, otherwise will be a value.
     */
    public function getPostMetaData($postId, $key, $single);

    /**
     * Save meta data attach to a post.
     *
     * @param int    $postID
     * @param string $postKey
     * @param mixed  $postValue
     *
     * @return mixed
     */
    public function savePostMetaData($postID, $postKey, $postValue);
}
