<?php
/**
 * Created by abelair.
 * Date: 2017-11-28
 * Time: 1:07 PM.
 */

namespace atkwp\controllers;


use atk4\ui\FormField\Generic;
use atkwp\interfaces\MetaFieldInterface;

class MetaFieldController implements MetaFieldInterface
{
    protected $baseName;
    protected $fields = [];

    public function __construct()
    {
    }

    public function addField($name, Generic $field, $metaKeyName = null)
    {
        if (!$metaKeyName) {
            $metaKeyName = '_'.$name;
        }

        $field->short_name = $metaKeyName;
        $this->fields[$name] = $field;
    }

    public function getField($name)
    {
        return $this->fields[$name];
    }

    public function getFields()
    {
        return $this->fields;
    }
}
