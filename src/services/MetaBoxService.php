<?php
/**
 * Created by abelair.
 * Date: 2017-11-20
 * Time: 10:12 AM
 */

namespace atkwp\services;

use atkwp\AtkWp;
use atkwp\interfaces\ComponentInterface;

class MetaBoxService
{
    private $ctrl;
    protected $executable;

    public $metaBoxes = [];
    //public $metaDisplayCount = 0;

    public function __construct(ComponentInterface $ctrl, $metaBoxes, $callable)
    {
        $this->ctrl = $ctrl;
        $this->executable = $callable;
        $this->setMetaBoxes($metaBoxes);
        $this->loadMetaBoxes();
        //register panel components with ctrl ounce fully loaded and with hook setting in place.
        add_action('admin_init', function(){
            $this->ctrl->registerComponents('metaBox', $this->getMetaBoxes());
        });
    }

//    public function setCurrentMetaBox( \WP_Post $post, $args)
//    {
//        $this->currentMetaBox['post'] = $post;
//        $this->currentMetaBox['args'] = $args;
//    }

    /**
     * Load metaBoxes define in our config file.
     */
    public function loadMetaBoxes()
    {
        foreach ($this->metaBoxes as $key => $metaBox) {
            $this->registerMetaBox($key, $metaBox);
        }
    }

    public function getMetaBoxes()
    {
        return $this->metaBoxes;
    }

    public function setMetaBoxes($metaBoxes)
    {
        //add id key to our panels
        foreach($metaBoxes as $key => $metaBox) {
            $metaBoxes[$key]['id'] = $key;
            //hook use by WP when generating a post admin page, i.e. post, page, comments etc.
            //We setup the hook here so our Enqueue service can find this component and load proper js/css files with it.
            $metaBoxes[$key]['hook'] = 'post.php';
        }
        $this->metaBoxes = $metaBoxes;
    }

    public function registerMetaBox($key, $metabox)
    {
        //create metaBoxes using closure function.
        add_action('add_meta_boxes', function() use ($key, $metabox) {
            $args = (isset($metabox['args']))? $metabox['args'] : null;
            add_meta_box(
                $key,
                $metabox['title'],
                $this->executable,
                $metabox['type'],
                $metabox['context'],
                $metabox['priority'],
                $args
            );
        });
        //Add save post action
        add_action('save_post_'.$metabox['type'], [$this, 'savePostType'], 10, 3 );
    }

    /**
     * Save post is fire early in Wp and get redirect after post has been saved.
     * Redirection is done prior of adding metaBox to this app.
     *
     * In order to delegate the saving of metaBox field to the metaBox class, we
     * need to rebuild them within our app and call the savePost function.
     *
     * @param $post \WP_Post
     */
    public function savePostType($postId, \WP_Post $post, $isUpdating)
    {
        //Add new post will trigger the save post hook and isUpdating will be false
        // We do want to catch this for saving our meta field.
        if ($isUpdating) {
            foreach ($this->metaBoxes as $key => $metaBox) {
                //$box = $this->app->add($metaBox['uses'], ['name'=>$key, 'id'=>$key]);
                $box = new $metaBox['uses'];
                $box->savePost($postId, $this->ctrl);
            }
        }

    }

    /**
     * Return post meta data value associated to a post.
     * @param $postID
     * @param $postKey
     * @param bool $single
     *
     * @return mixed
     */
    public function getPostMetaData($postID, $postKey, $single = true)
    {
        return get_post_meta($postID, $postKey, true);
    }

    /**
     * Save Post meta data value.
     * @param $postID
     * @param $postKey
     * @param $postValue
     */
    public function savePostMetaData($postID, $postKey, $postValue)
    {
        update_post_meta($postID, $postKey, $postValue);
    }
}