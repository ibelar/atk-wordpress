<?php
/**
 * Created by abelair.
 * Date: 2017-06-09
 * Time: 2:07 PM
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

    protected $assetsUrl;

	protected $atkJsFiles = [
		'https://cdnjs.cloudflare.com/ajax/libs/jquery-serialize-object/2.5.0/jquery.serialize-object.min.js',
		'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.10/semantic.min.js',
		'https://cdn.rawgit.com/mdehoog/Semantic-UI-Calendar/0.0.8/dist/calendar.min.js',
		'https://cdn.rawgit.com/atk4/ui/1.3.0/public/atk4JS.min.js'
	];

	//the css file to load.
	protected $atkCssFiles = [
	    'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.10/semantic.css',
        'https://cdn.rawgit.com/mdehoog/Semantic-UI-Calendar/0.0.8/dist/calendar.css'
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

	public function enqueueAdminFiles($hook)
	{
        //Check if this is an atk component.
        //We need to load js and css for atk when using panel or metaBox
        if ($component = $this->ctrl->getComponentByType('panel', $hook, 'hook')) {}
        elseif ($component = $this->ctrl->getComponentByType('metaBox', $hook, 'hook')){};

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
                wp_enqueue_style( $file, $source, $file );
            }
        }
	}

	public function enqueueJsInclude($files)
	{
		foreach ($files as $file) {
			wp_enqueue_script($file);
		}
	}

	public function enqueueCssInclude($files)
	{
		foreach ($files as $file) {
			wp_enqueue_style($file);
		}
	}
}