<?php
/**
 * SugarCLI
 *
 * PHP Version 5.3 -> 5.4
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
     * @var array
     */
    public static $siPrefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );

    /**
     * Humanize the size bu add a Prefix
     *
     * @param integer $bytes
     * @param integer $base
     *
     * @return string Readable size
     */
    public static function humanize($bytes, $base = 1024)
    {
        if (empty($bytes)) {
            return '0 B';
        }
        $class = min((int)log($bytes, $base), count(static::$siPrefix) - 1);

        return sprintf('%1.2F %s', $bytes / pow($base, $class), static::$siPrefix[$class]);
    }
}
