<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 10/17/14 11:32 AM
 */
namespace Solve\DependencyInjection\Tests;


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../DependencyContainer.php';
require_once __DIR__ . '/../DependencyUnit.php';
require_once __DIR__ . '/Config.php';


use Solve\DependencyInjection\DependencyContainer;

class DependencyContainerTest extends \PHPUnit_Framework_TestCase {

    public function testBasic() {
        $dc = DependencyContainer::getMainInstance();
        $dc->add('config', 'Config', 'project');
        $this->assertTrue($dc->hasDependency('config'), 'Dependency registered');

        /**
         * @var \Config $config
         */
        $config = $dc->get('config');
        $this->assertInstanceOf('Config', $config, 'Config dependency got ok');

        $this->assertEquals('hello', $config->getHello(), 'Config method called');
        $id1 = $config->getInstanceId();

        /**
         * @var \Config $config2
         */
        $config2 = $dc->get('config');
        $id2 = $config2->getInstanceId();

        $this->assertEquals($id1, $id2, 'Using the same instance');
        $this->assertEquals('project', $config->getConfigParam(), 'Parameter passed to constructor');
    }
}
