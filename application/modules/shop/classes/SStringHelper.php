<?php

class SStringHelper
{

    /**
     * Перенос длинных строк
     * @param string $str
     * @param int $len
     * @param string $break
     * @param bool $cut
     * @return mixed|string
     */
    public static function wordwrap($str, $len = 50, $break = ' ', $cut = false) {

        if (empty($str)) {
            return '';
        }

        if (!$cut) {
            $pattern = '/(\S{' . $len . '})/u';
        } else {
            $pattern = '/(.{' . $len . '})/u';
        }

        return preg_replace($pattern, '\${1}' . $break, $str);
    }

    /**
     * Отменки слов
     * Пример:
     * $word[0] - 1 комментарий
     * $word[1] - 2 комментария
     * $word[2] - 5 комментариев
     * @param int $count
     * @param array $words
     * @return string
     */
    public static function Pluralize($count = 0, array $words = []) {
        return pluralize($count, $words);
    }

}