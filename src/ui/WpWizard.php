<?php
/**
 * Wizard implementation.
 */

namespace atkwp\ui;

use atk4\ui\Wizard;
use atkwp\helpers\WpUtil;

class WpWizard extends Wizard
{
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
