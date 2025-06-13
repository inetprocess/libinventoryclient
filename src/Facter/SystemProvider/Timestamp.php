<?php
/**
 * Inventory
 *
 * PHP Version 8.2
 * SugarCRM Versions 6.5 - 7.6
 *
 * @author Rémi Sauvat
 * @copyright 2005-2015 iNet Process
 *
 * @package inetprocess/inventory
 *
 * @license GNU General Public License v2.0
 *
 * @link http://www.inetprocess.com
 */

namespace Inet\Inventory\Facter\SystemProvider;

use Inet\Inventory\Facter\FacterInterface;

class Timestamp implements FacterInterface
{
    public function getFacts(): array
    {
        $date = new \DateTime();

        return ['facts_timestamp' => $date->getTimestamp()];
    }
}
