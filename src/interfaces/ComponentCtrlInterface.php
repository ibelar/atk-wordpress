<?php
/**
 * Created by abelair.
 * Date: 2017-11-24
 * Time: 10:33 AM
 */

namespace atkwp\interfaces;


interface ComponentCtrlInterface
{

    /**
     * Create services need for your plugin.
     * @return mixed
     */
    public function initializeComponents($plugin);
    public function registerComponents($type, $components);
    public function getComponentByType($type, $search, $searchKey);
    public function getComponentByKey($search, $components = []);
    public function getPostMetaData($postId, $key, $single);
    public function savePostMetaData($postID, $postKey, $postValue);
}