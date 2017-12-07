<?php
/**
 * Created by abelair.
 * Date: 2017-11-24
 * Time: 10:33 AM.
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
     * Get a component by type from the component container.
     *
     * @param string $type      The type of component to search.
     * @param string $search    The value to search for.
     * @param string $searchKey The key use to search, default to id.
     *
     * @return array||null The component definition.
     */
    public function getComponentByType($type, $search, $searchKey = 'id');

    /**
     * Search component by a key value in a components container.
     *
     * @param string $search     The value of the key to search for.
     * @param array  $components The component containers.
     *
     * @return array||null The component definition.
     */
    public function getComponentByKey($search, $components);

    /**
     * Get meta data attach to a post.
     *
     * @param integer $postId The post id.
     * @param string  $key    The meta key set in db to retrieve value for.
     * @param bool    $single Return single or multiple value.
     *
     * @return mixed Will be an array if single is false, otherwise will be a value.
     */
    public function getPostMetaData($postId, $key, $single);

    /**
     * Save meta data attach to a post.
     *
     * @param integer $postID
     * @param string  $postKey
     * @param mixed   $postValue
     *
     * @return mixed
     */
    public function savePostMetaData($postID, $postKey, $postValue);
}
