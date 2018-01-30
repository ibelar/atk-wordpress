<?php
/**
 * Wizard implementation.
 */

namespace atkwp\ui;

use atk4\ui\Wizard;
use atkwp\helpers\WpUtil;

class WpWizard extends Wizard
{

//    public function init()
//    {
//        parent::init();
//        $this->stepCallback = $this->add(['Callback', 'urlTrigger'=>$this->name]);
//
//        $this->currentStep = $this->stepCallback->triggered() ?: 0;
//
//        $this->stepTemplate = $this->template->cloneRegion('Step');
//        $this->template->del('Step');
//
//        // add buttons
//        if ($this->currentStep) {
//            $this->buttonPrev = $this->add(['Button', 'Back', 'basic'], 'Left');
//            $this->buttonPrev->link($this->stepCallback->getURL($this->currentStep - 1));
//        }
//
//        $this->buttonNext = $this->add(['Button', 'Next', 'primary'], 'Right');
//        $this->buttonFinish = $this->add(['Button', 'Finish', 'primary'], 'Right');
//
//        $this->buttonNext->link($this->stepCallback->getURL($this->currentStep + 1));
//    }
    public function url($page = [])
    {
        $args = [];
        if (WpUtil::isAdmin()) {
            $url = 'admin.php';
            $args['page'] = @$_REQUEST['page'];
            $args = array_merge($args, $page);
        } else {
            $url = $_SERVER['REQUEST_URI'];
        }

        $param = http_build_query($args);

        if ($param) {
            $url = $url.'?'.$param;
        }

        return $url;
    }
}
