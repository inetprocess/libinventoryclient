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

namespace Inet\Inventory\Facter;

use Symfony\Component\Process\Process;

class CommandProvider implements FacterInterface
{
    protected string $cmd;
    protected bool $as_json;

    public function __construct(string $command, bool $as_json = false)
    {
        $this->cmd = $command;
        $this->as_json = $as_json;
    }

    /**
     * Return the facts generated from the command.
     * If as_json is true, the command must produce valid json.
     */
    public function getFacts(): array
    {
        try {
            $output = $this->runCommand($this->cmd);
            if ($this->as_json) {
                $json = json_decode($output, true);
                if (is_null($json)) {
                    return [];
                }
                return $json;
            }
            return $this->parseFacts($output);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Run the command.
     *
     * @param string $cmd Command line to run
     * @return string Output of command
     */
    protected function runCommand(string $cmd): string
    {
        $process = Process::fromShellCommandline($cmd);
        $process->mustRun();
        return $process->getOutput();
    }

    /**
     * Parse flat results in the form
     * fact1=value1
     * fact2=value2
     */
    protected function parseFacts(string $facts_string): array
    {
        $facts = [];
        foreach (explode(PHP_EOL, $facts_string) as $line) {
            if (empty($line)) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $facts[$key] = $value;
        }
        return $facts;
    }
}
