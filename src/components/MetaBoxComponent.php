<?php
/**
 * Created by abelair.
 * Date: 2017-11-20
 * Time: 10:21 AM
 */

namespace atkwp\components;


class MetaBoxComponent extends PanelComponent
{
    public $metaFields = [];

    public function addFormField($field)
    {
        $this->metaFields[] = $field;
    }

    public function init()
    {
        parent::init();

        foreach ($this->metaFields as $field) {
            $this->add($field);
        }
    }
}