<?php
/**
 * Created by abelair.
 * Date: 2017-11-28
 * Time: 10:48 AM.
 */

namespace atkwp\interfaces;

use atk4\ui\FormField\Generic;

interface MetaFieldInterface
{
    /**
     * AddFields to a Generic field object container, usually an array.
     *
     * @param $name //the name of the field.
     * @param Generic $field //the atk field instance.
     * @param $baseName //metaKey name for your field in WP db.
     *
     * Note: using '_' in front of your meta key name, ex: _fieldName will
     * result in WP hiding the meta field in WP custom meta field box.
     */
    public function addField($name, Generic $field, $metaKeyName);

    /**
     * Retrieve field from container with Generic field object.
     *
     * @param $name //the name of the field to retreive.
     *
     * @return Generic FormField
     */
    public function getField($name);

    /**
     * Retrieve all fields from Generic fields container.
     *
     * @return mixed
     */
    public function getFields();
}
