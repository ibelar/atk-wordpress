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
 * Responsible for creating and registering all WP action
 * needed for metaboxes.
 */

namespace atkwp\services;

use atkwp\interfaces\ComponentCtrlInterface;

class MetaBoxService
{
    /**
     * The component controller responsible of initiating this service.
     *
     * @var ComponentCtrlInterface
     */
    private $ctrl;

    /**
     * The executable need to output metabox component within Wp.
     *
     * @var callable
     */
    protected $executable;

    /**
     * The metaboxes registered within this services.
     *
     * @var array
     */
    public $metaBoxes = [];

    /**
     * MetaBoxService constructor.
     *
     * @param ComponentCtrlInterface $ctrl      The component ctrl.
     * @param array                  $metaBoxes The list of metaboxes as defined in configuration.
     * @param callable               $callable  The executable need to run the metaboxes in WP.
     */
    public function __construct(ComponentCtrlInterface $ctrl, $metaBoxes, $callable)
    {
        $this->ctrl = $ctrl;
        $this->executable = $callable;
        $this->setMetaBoxes($metaBoxes);
        $this->registerMetaBoxes();
        //register panel components with ctrl when metaboxes are fully loaded and with hook setting in place.
        add_action('admin_init', function () {
            $this->ctrl->registerComponents('metaBox', $this->getMetaBoxes());
        });
    }

    /**
     * Perform some initialisation to our metaboxes prior to register them.
     *
     * @param array $metaBoxes
     */
    public function setMetaBoxes($metaBoxes)
    {
        //add id key to our panels
        foreach ($metaBoxes as $key => $metaBox) {
            $metaBoxes[$key]['id'] = $key;
            //hook use by WP when generating a post admin page, i.e. post, page, comments etc.
            //We setup the hook here so our Enqueue service can find this component and load proper js/css files with it.
            $metaBoxes[$key]['hook'] = 'post.php';
        }
        $this->metaBoxes = $metaBoxes;
    }

    /**
     * Registers each metaboxes within WP.
     */
    public function registerMetaBoxes()
    {
        foreach ($this->metaBoxes as $key => $metaBox) {
            $this->registerMetaBox($key, $metaBox);
        }
    }

    /**
     * Return all metaboxes.
     *
     * @return array
     */
    public function getMetaBoxes()
    {
        return $this->metaBoxes;
    }

    /**
     * Create WP action to define metabox.
     *
     * @param string $key     The metabox id.
     * @param array  $metabox The metabox configuration.
     */
    public function registerMetaBox($key, $metabox)
    {
        //create metaBoxes using closure function.
        add_action('add_meta_boxes', function () use ($key, $metabox) {
            $args = (isset($metabox['args'])) ? $metabox['args'] : null;
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
        add_action('save_post_'.$metabox['type'], [$this, 'savePostType'], 10, 3);
    }

    /**
     * Save post is fire early in Wp and get redirect after post has been saved.
     * Redirection is done prior of adding metaBox to this app.
     *
     * In order to delegate the saving of metaBox field to the metaBox class, we
     * need to rebuild and call their savePost function.
     *
     * @param $post \WP_Post
     */
    public function savePostType($postId, \WP_Post $post, $isUpdating)
    {
        //Add new post will trigger the save post hook and isUpdating will be false
        // We do want to catch this for saving our meta field.
        if ($isUpdating) {
            foreach ($this->metaBoxes as $key => $metaBox) {
                $box = new $metaBox['uses']();
                $box->savePost($postId, $this->ctrl);
            }
        }
    }

    /**
     * Return post meta data value associated to a post.
     *
     * @param int    $postID
     * @param string $postKey
     * @param bool   $single
     *
     * @return mixed
     */
    public function getPostMetaData($postID, $postKey, $single = true)
    {
        return get_post_meta($postID, $postKey, true);
    }

    /**
     * Save Post meta data value.
     *
     * @param int    $postID
     * @param string $postKey
     * @param mixed  $postValue
     *
     * @return mixed
     */
    public function savePostMetaData($postID, $postKey, $postValue)
    {
        return update_post_meta($postID, $postKey, $postValue);
    }
}
