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
 * Collects atk\ui\Input field into a field container array.
 * Fields can then be retrieve and add to a metabox component view.
 */

namespace atkwp\controllers;

use atk4\ui\View;
use atkwp\interfaces\MetaFieldInterface;

class MetaFieldController implements MetaFieldInterface
{
    protected $baseName;
    protected $fields = [];

    public function __construct()
    {
    }

    /**
     * Add field object to a container.
     *
     * @param string       $name        The name of the field.
     * @param View         $field       The field object to add to container.
     * @param null||string $metaKeyName The meta key name to save in db.
     */
    public function addField($name, View $field, $metaKeyName = null)
    {
        // Add default name if not supplied.
        // adding underscore prevent Wp to display in custom field setup.
        if (!$metaKeyName) {
            $metaKeyName = '_'.$name;
        }

        $field->short_name = $metaKeyName;
        $this->fields[$name] = $field;
    }

    /**
     * Return a field object from the container base on it's name.
     *
     * @param $name
     *
     * @return View The field object.
     */
    public function getField($name)
    {
        return $this->fields[$name];
    }

    /**
     * Return all fields object from the container.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}
