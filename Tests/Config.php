<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 10/17/14 11:37 AM
 */

use Solve\DependencyInjection\DependencyUnit;

class Config extends DependencyUnit {

    private $_instanceId;
    private $_configParam;

    public function __construct($param = null) {
        $this->_instanceId = md5(microtime(true));
        $this->_configParam = $param;
    }

    /**
     * @return mixed
     */
    public function getInstanceId() {
        return $this->_instanceId;
    }

    public function getConfigParam() {
        return $this->_configParam;
    }

    public function getHello() {
        return "hello";
    }

}