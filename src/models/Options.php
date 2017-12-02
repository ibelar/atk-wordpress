<?php
/**
 * Created by abelair.
 * Date: 2017-11-15
 * Time: 1:32 PM.
 */

namespace atkwp\models;

use atkwp\helpers\WpUtil;

class Options extends \atk4\data\Model
{
    public function init()
    {
        $this->table = WpUtil::getDbPrefix().'options';
        $this->id_field = 'option_id';
        parent::init();

        $this->addField('name', ['actual'=>'option_name']);
        $this->addField('value', ['actual'=>'option_value']);
        $this->addField('autoload');
    }

    public function getOptionValue($option, $default = null)
    {
        $value = $this->tryLoadBy('name', $option)->get('value');
        if (isset($value)) {
            $value = maybe_unserialize($value);
        } else {
            $value = $default;
        }
        return $value;
    }

    public function saveOptionValue($option, $value)
    {
        $this->tryLoadBy('name', $option);
        $this->set('value', maybe_serialize($value));
        $this->set('name', $option);
        $this->save();
    }
}
