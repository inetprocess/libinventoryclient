<?php

namespace Inet\Inventory\Tests;

use Guzzle\Service\Client as GClient;
use Guzzle\Service\Description\ServiceDescription;

/**
 * @group inventory
 */
class RealClientTest extends MockClientTest
{
    public $fqdn = 'testserver.inetprocess.fr';

    public function getClientType()
    {
        return 'real';
    }
}
