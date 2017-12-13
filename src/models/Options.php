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
 * The Wp options model map to db table options.
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

    /**
     * Get an option store in options table.
     *
     * @param string       $option  the option name to retrieve.
     * @param null||string $default the default value to use if option does not exist.
     *
     * @throws \Exception
     *
     * @return mixed|null
     */
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

    /**
     * Save an option name, value in options table.
     *
     * @param $option
     * @param $value
     *
     * @throws \Exception
     * @throws \atk4\data\Exception
     */
    public function saveOptionValue($option, $value)
    {
        $this->tryLoadBy('name', $option);
        $this->set('value', maybe_serialize($value));
        $this->set('name', $option);
        $this->save();
    }
}
