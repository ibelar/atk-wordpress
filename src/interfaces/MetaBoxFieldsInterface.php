<?php
/**
 * Created by abelair.
 * Date: 2017-11-28
 * Time: 1:01 PM
 */

namespace atkwp\interfaces;


interface MetaBoxFieldsInterface
{
    /**
     * Initialised Field use in meta box using a MetaFieldInterface $ctrl.
     * This function is called early in class constructor that implement this interface.
     * The ctrl pass as an argument to this function must also implement the MetaFieldInterface.
     * Metabox field needs to be defined early when a post is update.
     * @param MetaFieldInterface $ctrl
     */
    public function initFields(MetaFieldInterface $ctrl);

    /**
     * Give a chance to update raw data prior to save it to database.
     * For example, you could escape $data from <script> character using strip_tags function.
     *
     * @param $fieldName
     * @param $data
     *
     * @return mixed //string
     */
    public function updateRawData($fieldName, $data);
}