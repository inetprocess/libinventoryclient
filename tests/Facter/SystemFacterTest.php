<?php

namespace Inet\Inventory\Tests\Facter;

use Inet\Inventory\Facter\SystemFacter;

class SystemFacterTest extends \PHPUnit_Framework_TestCase
{

    public function testRegisterProvider()
    {
        $facter = new SystemFacter();
        $this->assertTrue(class_exists('Inet\Inventory\Facter\SystemProvider\Hostname'));
    }

    public function testgetFacts()
    {
        $facter = new SystemFacter();
        $facts = $facter->getFacts();
        $this->assertArrayHasKey('hostname', $facts);
    }
}
