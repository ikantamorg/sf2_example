<?php
/**
 * User: Dred
 * Date: 28.05.13
 * Time: 17:58
 */

namespace Domain\CoreBundle\Util;


class StringUtils
{

    private static $password_allow_chars = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890@#%$*';

    /**
     * This class should not be instantiated
     */
    private function __construct()
    {
    }


    /**
     * Generate random password
     *
     * @param int $length
     *
     * @return string
     */
    public static function generatePassword($length = 12)
    {
        $pwd = str_shuffle(self::$password_allow_chars);

        return substr($pwd, 0, $length);
    }

    /**
     * Camelize string
     *
     * @param string $word
     * @return string
     */
    public static function stringCamelize($word)
    {
        return preg_replace('/(^|_)([a-z])/e', 'strtoupper("\\2")', $word);
    }

    /**
     * Decamelize string
     *
     * @param string $word
     * @return string
     */
    public static function stringDecamelize($word)
    {
        return preg_replace(
            '/(^|[a-z])([A-Z])/e',
            'strtolower(strlen("\\1") ? "\\1_\\2" : "\\2")',
            $word
        );
    }
}
