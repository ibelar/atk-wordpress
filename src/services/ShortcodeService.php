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
 * needed for shortcode.
 */

namespace atkwp\services;

use atkwp\interfaces\ComponentCtrlInterface;

class ShortcodeService
{
    /**
     * The component controller responsible of initiating this service.
     *
     * @var ComponentCtrlInterface
     */
    private $ctrl;

    /**
     * The executable need to output shortcode component within Wp.
     *
     * @var callable
     */
    protected $executable;

    /**
     * The shortcode registered within this services.
     *
     * @var array
     */
    public $shortcodes = [];

    /**
     * ShortcodeService constructor.
     *
     * @param ComponentCtrlInterface $ctrl
     * @param array                  $shortcodes
     * @param callable               $callable
     */
    public function __construct(ComponentCtrlInterface $ctrl, array $shortcodes, $callable)
    {
        $this->ctrl = $ctrl;
        $this->executable = $callable;
        $this->setShortcodes($shortcodes);
        $this->registerShortcodes();

        //register shortcode component for front and ajax action.
        add_action('wp_loaded', function () {
            $this->ctrl->registerComponents('shortcode', $this->getShortcodes());
        });
    }

    /**
     * Return shortcode compoents.
     *
     * @return array
     */
    public function getShortcodes()
    {
        return $this->shortcodes;
    }

    /**
     * Set shortcodes.
     *
     * @param $shortcodes
     */
    public function setShortcodes($shortcodes)
    {
        foreach ($shortcodes as $key => $shortcode) {
            $shortcodes[$key]['id'] = $key;
            $shortcodes[$key]['enqueued'] = false;
        }
        $this->shortcodes = $shortcodes;
    }

    /**
     * Register each shortcode in Wp.
     */
    public function registerShortcodes()
    {
        if (isset($this->shortcodes)) {
            foreach ($this->shortcodes as $key => $shortcode) {
                $this->registerShortcode($key, $shortcode);
            }
        }
    }

    /**
     * The actual shortcode implementation in Wp.
     *
     * @param $key
     * @param $shortcode
     */
    public function registerShortcode($key, $shortcode)
    {
        add_shortcode($shortcode['name'], function ($args) use ($key, $shortcode) {
            if (!$this->shortcode[$shortcode['id']]['enqueued']) {
                $this->ctrl->enqueueShortcodeFiles($shortcode);
                $this->shortcode[$shortcode['id']]['enqueued'] = true;
            }

            return call_user_func_array($this->executable, [$shortcode, $args]);
        });
        $this->shortcodes[$key] = $shortcode;
    }
}
