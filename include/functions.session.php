<?php
/**
 * NewBB 5.0x,  the forum module for XOOPS project
 *
 * @copyright      XOOPS Project (https://xoops.org)
 * @license        GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author         Taiwen Jiang (phppp or D.J.) <phppp@users.sourceforge.net>
 * @since          4.00
 * @package        module::newbb
 */

use Xmf\Request;

defined('NEWBB_FUNCTIONS_INI') || require __DIR__ . '/functions.ini.php';
define('NEWBB_FUNCTIONS_SESSION_LOADED', true);

if (!defined('NEWBB_FUNCTIONS_SESSION')) {
    define('NEWBB_FUNCTIONS_SESSION', 1);

    /*
     * Currently the newbb session/cookie handlers are limited to:
     * -- one dimension
     * -- "," and "|" are preserved
     *
     */
    /**
     * @param              $name
     * @param string|array $string
     */
    function newbbSetSession($name, $string = '')
    {
        if (is_array($string)) {
            $value = [];
            foreach ($string as $key => $val) {
                $value[] = $key . '|' . $val;
            }
            $string = implode(',', $value);
        }
        $_SESSION['newbb_' . $name] = $string;
    }

    /**
     * @param             $name
     * @param bool        $isArray
     * @return array|bool
     */
    function newbbGetSession($name, $isArray = false)
    {
        $value = !empty($_SESSION['newbb_' . $name]) ? $_SESSION['newbb_' . $name] : false;
        if ($isArray) {
            $_value = $value ? explode(',', $value) : [];
            $value  = [];
            if (count($_value) > 0) {
                foreach ($_value as $string) {
                    $key         = mb_substr($string, 0, mb_strpos($string, '|'));
                    $val         = mb_substr($string, mb_strpos($string, '|') + 1);
                    $value[$key] = $val;
                }
            }
            unset($_value);
        }

        return $value;
    }

    /**
     * @param              $name
     * @param string|array $string
     * @param int          $expire
     */
    function newbbSetCookie($name, $string = '', $expire = 0)
    {
        global $forumCookie;
        if (is_array($string)) {
            $value = [];
            foreach ($string as $key => $val) {
                $value[] = $key . '|' . $val;
            }
            $string = implode(',', $value);
        }
        setcookie($forumCookie['prefix'] . $name, $string, (int)$expire, $forumCookie['path'], $forumCookie['domain'], $forumCookie['secure']);
    }

    /**
     * @param             $name
     * @param bool        $isArray
     * @return array|null|string
     */
    function newbbGetCookie($name, $isArray = false)
    {
        global $forumCookie;
        //        $value = !empty($_COOKIE[$forumCookie['prefix'] . $name]) ? $_COOKIE[$forumCookie['prefix'] . $name] : null;
        $value = Request::getString($forumCookie['prefix'] . $name, null, 'COOKIE');

        if ($isArray) {
            $_value = $value ? explode(',', $value) : [];
            $value  = [];
            if (count($_value) > 0) {
                foreach ($_value as $string) {
                    $sep = mb_strpos($string, '|');
                    if (false === $sep) {
                        $value[] = $string;
                    } else {
                        $key         = mb_substr($string, 0, $sep);
                        $val         = mb_substr($string, $sep + 1);
                        $value[$key] = $val;
                    }
                }
            }
            unset($_value);
        }

        return $value;
    }
}
