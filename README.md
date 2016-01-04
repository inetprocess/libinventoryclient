# inetprocess/libinventoryclient [![Build Status](https://travis-ci.org/inetprocess/libinventoryclient.svg)](https://travis-ci.org/inetprocess/libinventoryclient)

PHP library to fetch information from the system and SugarCRM and send it to an inventory server (REST API)

## Facter
Fetch informations about the system or a SugarCRM Instance and return a key, value PHP array.
### Usage
#### System Facter
```php
use Inet\Inventory\Facter\SystemFacter;
$facter = new SystemFacter();
$facts = $facter->getFacts();
var_dump($facts);
```
```
array(20) {
  'system_uptime' =>
  array(4) {
    'seconds' =>
    int(2353764)
    'hours' =>
    double(653)
    'days' =>
    double(27)
    'uptime' =>
    string(40) "27 days, 5 hours, 49 minutes, 54 seconds"
  }
  'architecture' =>
  string(6) "x86_64"
  ...
```
#### SugarCRM Facter
```php
use Psr\Log\NullLogger;
use Inet\SugarCRM\Application;
use Inet\SugarCRM\Database\SugarPDO;
use Inet\Inventory\Facter\SugarFacter;

$app = new Application(new NullLogger(), 'path/to/sugarcrm');
$facter = new SugarFacter($app, new SugarPDO($app));
$facts = $facter->getFacts();
var_dump($facts);
```
```
array(17) {
  'version' =>
  string(7) "7.6.0.0"
  'db_version' =>
  string(7) "7.6.0.0"
  'flavor' =>
  string(3) "PRO"
  'build' =>
  string(4) "1552"
  'build_timestamp' =>
  string(18) "2015-06-05 03:29pm"
  ...
```
#### Combine facters
```php
use Inet\Inventory\Facter\MultiFacterFacter;
$facter = new MultiFacterFacter(array($system_facter));
$facter->addFacter($sugar_facter);
$facts = $facter->getFacts();
```

## Agent
Send information to a REST API. 
Three entities are used: 
* Account : Client name.
* Server : Sugarcrm host and OS informations.
* SugarCRM instance: Installed SugarCRM instance. It is related to an account and a server.
The API is described in `src/InventoryService.json`.
