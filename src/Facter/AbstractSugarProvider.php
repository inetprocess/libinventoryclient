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

use PDO;
use Symfony\Component\Process\Process;
use Inet\SugarCRM\Application;

abstract class AbstractSugarProvider implements FacterInterface
{
    protected Application $sugarApp;
    protected PDO $pdo;

    abstract public function getFacts(): array;

    public function __construct(Application $sugarApp, PDO $pdo)
    {
        $this->sugarApp = $sugarApp;
        $this->pdo = $pdo;
    }

    public function getApplication(): Application
    {
        return $this->sugarApp;
    }

    public function getPath(): string
    {
        return $this->getApplication()->getPath();
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function queryOne(\PDOStatement $stmt): mixed
    {
        $value = null;
        $stmt->execute();
        if ($stmt !== false) {
            $result = $stmt->fetchAll();
            if (!empty($result)) {
                $value = $result[0][0];
            }
        }
        return $value;
    }

    private function realExec(string $cmd, bool $throw_exception, ?string $cwd): string
    {
        $process = Process::fromShellCommandline($cmd);
        if ($cwd !== null) {
            $process->setWorkingDirectory($cwd);
        }
        if ($throw_exception) {
            $process->mustRun();
        } else {
            $process->run();
        }
        return $process->getOutput();
    }

    public function exec(string $cmd, ?string $cwd = null): string
    {
        return $this->realExec($cmd, false, $cwd);
    }

    public function mustExec(string $cmd, ?string $cwd = null): string
    {
        return $this->realExec($cmd, true, $cwd);
    }
}
