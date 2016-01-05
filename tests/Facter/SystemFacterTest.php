<?php

namespace Inet\Inventory\Tests\Facter;

use Inet\Inventory\Facter\SystemFacter;
use Inet\Inventory\Facter\SystemProvider\Hostname;
use Inet\Inventory\Facter\SystemProvider\Lsb;

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

    public function testLsbAbsent()
    {
        $path = getenv('PATH');
        putenv('PATH=/');
        $facter = new Lsb();
        $facts = $facter->getFacts();
        putenv("PATH=$path");
        $this->assertEquals(array(), $facts);
    }

    public function testHostnameAbsent()
    {
        $path = getenv('PATH');
        putenv('PATH=/');
        $facter = new Hostname();
        $facts = $facter->getFacts();
        putenv("PATH=$path");
        $this->assertArrayHasKey('hostname', $facts);
        $this->assertArrayHasKey('fqdn', $facts);
        $this->assertNotEmpty($facts['hostname']);
        $this->assertEquals($facts['hostname'], $facts['fqdn']);
    }

    public function testInvalidPath()
    {
        $path = getenv('PATH');
        putenv('PATH=/');
        $facter = new SystemFacter();
        $facts = $facter->getFacts();
        putenv("PATH=$path");
        $this->assertEquals($path, getenv('PATH'));
    }
}
