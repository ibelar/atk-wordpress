<?php
/**
 * Created by abelair.
 * Date: 2017-11-30
 * Time: 11:30 AM.
 */

namespace atkwp\interfaces;

interface MetaBoxArgumentsInterface
{
    /**
     * This method is called prior to display metabox in WP.
     *
     * @param $args //$args set in configuration
     *
     * @return mixed
     */
    public function onMetaBoxArguments($args);
}
