<?php
/**
 * Created by abelair.
 * Date: 2017-12-11
 * Time: 9:01 AM
 */

namespace atkwp\services;

use atkwp\components\ShortcodeComponent;
use atkwp\interfaces\ComponentCtrlInterface;

class ShortcodeService
{
    private $ctrl;
    public $shortcodes = [];
    public $executable;

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

    public function getShortcodes()
    {
        return $this->shortcodes;
    }

    public function setShortcodes($shortcodes)
    {
        foreach ($shortcodes as $key => $shortcode) {
            $shortcodes[$key]['id'] = $key;
            $shortcodes[$key]['enqueued'] = false;
        }
        $this->shortcodes = $shortcodes;
    }

    public function registerShortcodes()
    {
        if (isset($this->shortcodes)) {
            foreach ($this->shortcodes as $key => $shortcode) {
                $this->registerShortcode($key, $shortcode);
            }
        }
    }

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
