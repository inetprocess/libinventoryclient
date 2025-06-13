<?php
/**
 * SugarCLI
 *
 * PHP Version 8.2
 * SugarCRM Versions 6.5 - 7.6
 *
 * @author Rémi Sauvat
 * @author Emmanuel Dyan
 * @copyright 2005-2015 iNet Process
 *
 * @package inetprocess/sugarcrm
 *
 * @license GNU General Public License v2.0
 *
 * @link http://www.inetprocess.com
 */

namespace Inet\Inventory;

/**
 * Various Utils
 */
class Utils
{
    /**
     * List of Prefixes
     *
     * @var array<string>
     */
    public static array $siPrefix = ['B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB'];

    /**
     * Humanize the size by adding a Prefix
     *
     * @param float $bytes
     * @param int $base
     *
     * @return string Readable size
     */
    public static function humanize(float $bytes, int $base = 1024): string
    {
        if (empty($bytes)) {
            return '0 B';
        }
        $class = min((int)log($bytes, $base), count(static::$siPrefix) - 1);

        return sprintf('%1.2F %s', $bytes / pow($base, $class), static::$siPrefix[$class]);
    }
}
