<?php
/**
 * This service is responsible to load js and css file within WP.
 */

namespace atkwp\services;

use atkwp\interfaces\ComponentCtrlInterface;

class EnqueueService
{
    private $ctrl;

    //All jsFiles to load
    protected $jsFiles = [];

    //All Css files to load
    protected $cssFiles = [];

    //Js files already register with WP
    protected $jsRegistered = [];

    //The url to your plugin assets files.
    protected $assetsUrl;

    protected $atkJsFiles = [
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-serialize-object/2.5.0/jquery.serialize-object.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.10/semantic.min.js',
        'https://cdn.rawgit.com/mdehoog/Semantic-UI-Calendar/0.0.8/dist/calendar.min.js',
        'https://cdn.rawgit.com/atk4/ui/1.3.0/public/atk4JS.min.js',
    ];

    //the css file to load.
    protected $atkCssFiles = [
        'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.10/semantic.css',
        'https://cdn.rawgit.com/mdehoog/Semantic-UI-Calendar/0.0.8/dist/calendar.css',
    ];

    public function __construct(ComponentCtrlInterface $ctrl, $enqueueFiles, $url)
    {
        $this->ctrl = $ctrl;
        $this->assetsUrl = $url;
        if (is_admin()) {
            if (isset($enqueueFiles['admin']['js']) && is_array($enqueueFiles['admin']['js'])) {
                $this->jsFiles = array_merge($this->jsFiles, $enqueueFiles['admin']['js']);
            }
            if (isset($enqueueFiles['admin']['css']) && is_array($enqueueFiles['admin']['css'])) {
                $this->jsFiles = array_merge($this->jsFiles, $enqueueFiles['admin']['css']);
            }
            add_action('admin_enqueue_scripts', [$this, 'enqueueAdminFiles']);
        } else {
            if (isset($enqueueFiles['front']['js']) && is_array($enqueueFiles['front']['js'])) {
                $this->jsFiles = array_merge($this->jsFiles, $enqueueFiles['front']['js']);
            }
            if (isset($enqueueFiles['front']['css']) && is_array($enqueueFiles['front']['css'])) {
                $this->jsFiles = array_merge($this->jsFiles, $enqueueFiles['front']['css']);
            }
            add_action('wp_enqueue_scripts', [$this, 'enqueueFrontFiles']);
        }
    }

    /**
     * WP action function when running under admin mode.
     *
     * @param $hook
     */
    public function enqueueAdminFiles($hook)
    {
        // Check if this is an atk component.
        // We need to load js and css for atk when using panel or metaBox
        if ($component = $this->ctrl->searchComponentByType('panel', $hook, 'hook')) {
        } elseif ($hook === 'post.php') {
            // if we are here, mean that we are editing a post.
            // check it's type and see if a metabox is using this type.
            if ($postType = get_post_type($_GET['post'])) {
                $component = $this->ctrl->searchComponentByType('metaBox', $postType, 'type');
            }
        } elseif ($hook === 'post-new.php') {
            if ($postType = @$_GET['post_type']) {
                // Check if we have a metabox that is using this post type.
                $component = $this->ctrl->searchComponentByType('metaBox', $postType, 'type');
            } else {
                //if not post_type set, this mean that we have a regular post.
                //Check if a metabox using post.
                $component = $this->ctrl->searchComponentByType('metaBox', 'post', 'type');
            }
        } elseif ($hook === 'index.php') {
            // if we are here mean that we are in dashboard page.
            // for now, just load atk js file if we are using dashboard.
            $component = $this->ctrl->getComponentsByType('dashboard');
        }

        if (isset($component)) {
            $this->jsFiles = array_merge($this->jsFiles, $this->atkJsFiles);
            $this->cssFiles = array_merge($this->cssFiles, $this->atkCssFiles);

            //check if component require specific js or css file.
            if (isset($component['js']) && !empty($component['js'])) {
                $this->jsFiles = array_merge($this->jsFiles, $component['js']);
            }
            if (isset($component['css']) && !empty($component['css'])) {
                $this->cssFiles = array_merge($this->cssFiles, $component['css']);
            }
            if (isset($component['js-inc']) && !empty($component['js-inc'])) {
                $this->jsRegistered = array_merge($this->jsRegistered, $component['js-inc']);
            }
        }

        if (!empty($this->jsFiles)) {
            $this->enqueueFiles($this->jsFiles, 'js');
        }
        if (!empty($this->cssFiles)) {
            $this->enqueueFiles($this->cssFiles, 'css');
        }
        if (!empty($this->jsRegistered)) {
            $this->enqueueJsInclude($this->jsRegistered);
        }
    }

    /**
     * WP action function when running in front end.
     */
    public function enqueueFrontFiles()
    {
        if (!empty($this->jsFiles)) {
            $this->enqueueFiles($this->jsFiles, 'js');
        }
        if (!empty($this->cssFiles)) {
            $this->enqueueFiles($this->cssFiles, 'css');
        }
        if (!empty($this->jsRegistered)) {
            $this->enqueueJsInclude($this->jsRegistered);
        }
    }

    /**
     * The actual file inclusion in WP.
     *
     * @param array  $files    The list of files to include.
     * @param string $type     The type of file to include, js or css.
     * @param null   $required The required file to include if needed.
     */
    public function enqueueFiles($files, $type, $required = null)
    {
        if ($type === 'js') {
            foreach ($files as $file) {
                if (strpos($file, 'http') === 0) {
                    $source = $file;
                } else {
                    $source = "{$this->assetsUrl}/js/{$file}.js";
                }
                //load in footer
                wp_enqueue_script($file, $source, $required, false, true);
            }
        } else {
            foreach ($files as $file) {
                if (strpos($file, 'http') === 0) {
                    $source = $file;
                } else {
                    $source = "{$this->assetsUrl}/css/{$file}.css";
                }
                wp_enqueue_style($file, $source, $file);
            }
        }
    }

    /**
     * The js files to includes that are already registered within WP.
     *
     * @param array $files
     */
    public function enqueueJsInclude(array $files)
    {
        foreach ($files as $file) {
            wp_enqueue_script($file);
        }
    }

    /**
     * The css files to include that are already registered withing WP.
     *
     * @param array $files
     */
    public function enqueueCssInclude(array $files)
    {
        foreach ($files as $file) {
            wp_enqueue_style($file);
        }
    }
}
