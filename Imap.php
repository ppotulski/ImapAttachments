<?php

class Imap
{
    /**
     * =?x-unknown?B?
     * =?iso-8859-1?Q?
     * =?windows-1252?B?
     *
     * @param string $string
     * @param string $base (optional) charset (IANA, lowercase)
     * @return string UTF-8
     */
    public static function decodeToUTF8($string, $base = 'windows-1252')
    {
        $pairs = [
            '?x-unknown?' => "?$base?"
        ];

        $string = strtr($string, $pairs);

        return imap_utf8($string);
    }
}