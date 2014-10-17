<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 24.11.13 23:26
 */

namespace Solve\DependencyInjection;


/**
 * Class DependencyUnit
 * @package Solve\Dependency
 *
 * Class DependencyUnit is a unit instance for dependencies
 *
 * @version 1.0
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 */
abstract class DependencyUnit {

    /**
     * @var DependencyContainer
     */
    protected $_dependencyContainer;

    public function setDependencyContainer(DependencyContainer $container) {
        $this->_dependencyContainer = $container;
    }

    public static function getDependencyInstance($parameters) {
        $className = get_called_class();
        if (is_callable(array($className, 'getInstance'))) {
            return call_user_func(array($className, 'getInstance'), $parameters);
        } else {
            return new $className($parameters);
        }
    }

} 