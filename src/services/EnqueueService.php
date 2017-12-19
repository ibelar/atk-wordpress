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
 * This service is responsible to load js and css file within WP.
 */

namespace atkwp\services;

use atkwp\interfaces\ComponentCtrlInterface;

class EnqueueService
{
    /**
     * The component controller responsible of initiating this service.
     *
     * @var ComponentCtrlInterface
     */
    private $ctrl;

    protected $semanticUiVersion = '2.2.12';
    protected $semanticCalanderVersion = '0.0.8';
    protected $atk4JsVersion = '1.3.0';

    /**
     * The js files to load.
     *
     * @var array
     */
    protected $jsFiles = [];

    /**
     * The css files to load.
     *
     * @var array
     */
    protected $cssFiles = [];

    /**
     * The js files already registered within Wp.
     *
     * @var array
     */
    protected $jsRegistered = [];

    /**
     * The url to the vendor directory.
     *
     * @var string
     */
    protected $vendorUrl;

    /**
     * The url to this assets directory.
     *
     * @var string
     */
    protected $atkWpAssetsUrl;

    /**
     * The url to the plugin assest directory.
     *
     * @var string
     */
    protected $assetsUrl;

    /**
     * EnqueueService constructor.
     *
     * @param ComponentCtrlInterface $ctrl
     * @param array                  $enqueueFiles
     * @param string                 $assetUrl
     * @param string                 $vendorUrl
     */
    public function __construct(ComponentCtrlInterface $ctrl, array $enqueueFiles, $assetUrl, $vendorUrl)
    {
        $this->ctrl = $ctrl;
        $this->assetsUrl = $assetUrl;
        $this->vendorUrl = $vendorUrl;
        $this->atkWpAssetsUrl = $vendorUrl.'/ibelar/atk-wordpress/assets';

        if (is_admin()) {
            if (isset($enqueueFiles['admin']['js']) && is_array($enqueueFiles['admin']['js'])) {
                $this->jsFiles = array_merge($this->jsFiles, $enqueueFiles['admin']['js']);
            }
            if (isset($enqueueFiles['admin']['css']) && is_array($enqueueFiles['admin']['css'])) {
                $this->cssFiles = array_merge($this->cssFiles, $enqueueFiles['admin']['css']);
            }
            add_action('admin_enqueue_scripts', [$this, 'enqueueAdminFiles']);
        } else {
            if (isset($enqueueFiles['front']['js']) && is_array($enqueueFiles['front']['js'])) {
                $this->jsFiles = array_merge($this->jsFiles, $enqueueFiles['front']['js']);
            }
            if (isset($enqueueFiles['front']['css']) && is_array($enqueueFiles['front']['css'])) {
                $this->cssFiles = array_merge($this->cssFiles, $enqueueFiles['front']['css']);
            }
            add_action('wp_enqueue_scripts', [$this, 'enqueueFrontFiles']);
        }
    }


    /**
     * Register js and css files necessary for our components.
     */
    protected function registerAtkWpFiles()
    {
        wp_register_script(
            'semantic',
            "{$this->atkWpAssetsUrl}/external/semantic-ui-{$this->semanticUiVersion}/semantic.min.js",
            [],
            $this->semanticUiVersion,
            true
        );

        wp_register_script(
            'semantic-calendar',
            "{$this->atkWpAssetsUrl}/external/mdehoog/Semantic-UI-Calendar/{$this->semanticCalanderVersion}/calendar.min.js",
            [],
            $this->semanticCalanderVersion,
            true
        );

        /*
         * Register our js files.
         * Because we declare dependencies for atk4JS, then calling wp_enqueue_script('atk4JS') will also load
         * these dependencies.
         */
        wp_register_script(
            'atk4JS',
            "{$this->atkWpAssetsUrl}/external/atk4/ui/{$this->atk4JsVersion}/atk4JS.min.js",
            ['jquery-serialize-object', 'semantic', 'semantic-calendar'],
            $this->atk4JsVersion,
            true
        );

        wp_register_style(
            'semantic',
            "{$this->atkWpAssetsUrl}/external/semantic-ui-{$this->semanticUiVersion}/semantic.min.css",
            [],
            $this->semanticUiVersion
        );

        wp_register_style(
            'semantic-calendar',
            "{$this->atkWpAssetsUrl}/external/mdehoog/Semantic-UI-Calendar/{$this->semanticCalanderVersion}/calendar.min.css",
            [],
            $this->semanticCalanderVersion
        );

        // Admin section css fix for certain semantic ui element.
        wp_register_style(
            'atk-wp',
            "{$this->atkWpAssetsUrl}/css/atk-wordpress.css",
            ['semantic', 'semantic-calendar'],
            null
        );
    }

    /**
     * WP action function when running under admin mode.
     *
     * @param $hook
     */
    public function enqueueAdminFiles($hook)
    {
        $this->registerAtkWpFiles();
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

            //Load our register atk js and css.
            wp_enqueue_script('atk4JS');
            wp_enqueue_style('atk-wp');
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
        $this->registerAtkWpFiles();

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
     * Shortcode need to run in Wp front.
     * This method is used to directly enqueue files when shortcode need them.
     *
     * @param $shortcode
     */
    public function enqueueShortCodeFiles($shortcode)
    {
        if ($shortcode['atk']) {
            $this->enqueueJsInclude(['atk4JS']);
            $this->enqueueCssInclude(['semantic', 'semantic-calendar']);
        }

        if (!empty($jsFiles)) {
            $this->enqueueFiles($jsFiles, 'js');
        }
        if (!empty($cssFiles)) {
            $this->enqueueFiles($cssFiles, 'css');
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
