<?php


namespace Solve\DependencyInjection;


use Solve\Storage\ArrayStorage;

/**
 * Class DependencyContainer
 * @package Solve\DependencyInjection
 *
 * Class DependencyContainer is a DI pattern realization
 *
 * @version 1.0
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 */
class DependencyContainer {

    /**
     * @var ArrayStorage
     */
    private $_dependencies;

    /**
     * @var ArrayStorage
     */
    private $_customArguments;

    private static $_mainInstance;

    public function __construct($initialDependencies = null) {
        $this->_dependencies    = new ArrayStorage();
        $this->_customArguments = new ArrayStorage();
        if (!empty($initialDependencies)) {
            $this->addDependencies($initialDependencies);
        }
    }

    public static function getMainInstance($initialDependencies = array()) {
        if (empty(self::$_mainInstance)) {
            self::$_mainInstance = new DependencyContainer($initialDependencies);
        }
        return self::$_mainInstance;
    }

    public function addDependencies($dependencies, $overwrite = true) {
        foreach ($dependencies as $name => $dependencyInfo) {
            if ($overwrite || !$this->hasDependency($name)) {
                $this->add($name, $dependencyInfo['className'], $dependencyInfo);
            }
        }
    }

    /**
     * Add dependency declaration to current container
     *
     * @param string $name
     * @param mixed $dependencyUnit
     * @param array $arguments
     */
    public function add($name, $dependencyUnit, $arguments = array()) {
        if (is_object($dependencyUnit)) {
            $dependencyClass = get_class($dependencyUnit);
        } else {
            $dependencyClass = $dependencyUnit;
        }
        if (!is_array($arguments)) $arguments = array('arguments' => $arguments);

        $this->_dependencies[$name] = array_merge($arguments, array(
            'className' => $dependencyClass,
            'object'    => !empty($arguments['object']) ? $arguments['object'] : null
        ));
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function get($name) {
        $instance = null;
        if ($this->_dependencies->offsetExists($name)) {
            $this->initializeDependencyObject($name);
            /** add reusability check */
            $instance = $this->_dependencies[$name]['object'];
        } else {
            throw new \Exception('Invalid dependency unit requested: ' . $name);
        }
        return $instance;
    }

    public function getAllDependencies() {
        return $this->_dependencies;
    }

    private function initializeDependencyObject($name) {
        $params = $this->_dependencies[$name];
        if (empty($params['object'])) {
            $arguments = isset($params['arguments']) ? (is_array($params['arguments']) ? $params['arguments'] : array($params['arguments'])) : array();
            if ($this->_customArguments->has($name)) {
                $this->_customArguments->extendDeepValue($arguments, $name);
                $arguments = $this->_customArguments->getDeepValue($name);
            }
            if (is_callable(array($params['className'], 'getInstance'))) {
                $instance = call_user_func_array(array($params['className'], 'getInstance'), $arguments);
            } else {
                if (count($arguments)) {
                    if (count($arguments) > 1) {
                        $r        = new \ReflectionClass($params['className']);
                        $instance = $r->newInstanceArgs($arguments);
                    } else {
                        $instance = new $params['className']($arguments[0]);
                    }
                } else {
                    $instance = new $params['className']();
                }
            }
            if ($instance instanceof DependencyUnit) {
                $instance->setDependencyContainer($this);
            }
            $params['object'] = $instance;
        }
        $this->_dependencies[$name] = $params;
    }

    /**
     * Check whether dependency has been already registered
     * @param $name
     * @return bool
     */
    public function hasDependency($name) {
        return $this->_dependencies->offsetExists($name);
    }

    public function setDependencyObject($name, $object) {
        $this->_dependencies[$name]['object'] = $object;
        return $this;
    }

    public function removeDependency($name) {
        if ($this->hasDependency($name)) {
            $this->_dependencies->offsetUnset($name);
        }
        return $this;
    }

    /**
     *
     * @param $name
     * @param $argument
     * @return DependencyContainer
     */
    public function addDependencyArgument($name, $argument) {
        if (!$this->_customArguments->offsetExists($name)) {
            $this->_customArguments[$name] = array();
        }
        if (is_array($argument)) {
            $this->_customArguments->extendDeepValue($argument, $name);
        } else {
            $this->_customArguments[$name][] = $argument;
        }
        return $this;
    }

    public function setDependencyArguments($name, $arguments) {
        $this->_customArguments[$name] = $arguments;
        return $this;
    }

}