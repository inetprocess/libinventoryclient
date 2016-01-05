<?php
/**
 * Inventory
 *
 * PHP Version 5.3 -> 5.4
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
    protected $sugarApp;
    protected $pdo;

    abstract public function getFacts();

    public function __construct(Application $sugarApp, PDO $pdo)
    {
        $this->sugarApp = $sugarApp;
        $this->pdo = $pdo;
    }

    public function getApplication()
    {
        return $this->sugarApp;
    }

    public function getPath()
    {
        return $this->getApplication()->getPath();
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function queryOne(\PDOStatement $stmt)
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

    private function realExec($cmd, $throw_exception, $cwd)
    {
        $process = new Process($cmd, $cwd);
        if ($throw_exception) {
            $process->mustRun();
        } else {
            $process->run();
        }
        return $process->getOutput();
    }

    public function exec($cmd, $cwd = null)
    {
        return $this->realExec($cmd, false, $cwd);
    }

    public function mustExec($cmd, $cwd = null)
    {
        return $this->realExec($cmd, true, $cwd);
    }
}
