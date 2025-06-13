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

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Inet\Inventory\Facter\FacterInterface;

class Hostname implements FacterInterface
{
    public function getFacts(): array
    {
        $hostname = $fqdn = gethostname();
        try {
            $process = Process::fromShellCommandline('hostname --fqdn');
            $process->mustRun();
            $fqdn = trim($process->getOutput());
        } catch (ProcessFailedException $e) {
            // Keep default fqdn if command fails
        }

        return [
            'fqdn' => $fqdn,
            'hostname' => gethostname()
        ];
    }
}
