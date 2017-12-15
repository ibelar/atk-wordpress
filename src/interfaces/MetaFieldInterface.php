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
 * Metafield controller interface.
 * A metabox component using the MetaBoxFieldsInterface must use a field controller using this interface.
 */

namespace atkwp\interfaces;

use atk4\ui\View;

interface MetaFieldInterface
{
    /**
     * AddFields to a Generic field object container, usually an array.
     *
     * @param string $name     the name of the field.
     * @param View   $field    the atk field instance.
     * @param string $baseName metaKey name for your field in WP db.
     *
     * Note: using '_' in front of your meta key name, ex: _fieldName will
     * result in WP hiding the meta field in WP custom meta field box.
     */
    public function addField($name, View $field, $metaKeyName);

    /**
     * Retrieve field from container with Generic field object.
     *
     * @param $name //the name of the field to retreive.
     *
     * @return View FormField
     */
    public function getField($name);

    /**
     * Retrieve all fields from a Generic fields container.
     *
     * @return mixed
     */
    public function getFields();
}
