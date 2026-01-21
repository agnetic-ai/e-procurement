<?php
class FormatMoneyHelper
{
    public static function formatMoney($value)
    {
        if ($value === null || $value === '') {
            return '';
        }

        $value = preg_replace('/[^0-9]/', '', $value);

        if ($value === '') {
            return '';
        }

        return number_format((int)$value, 0, ',', '.');
    }


    public static function unformatMoneyValue($value)
    {
        if (!$value) {
            return 0;
        }

        $value = trim((string)$value);
        $value = preg_replace('/[^0-9]/', '', $value);

        return (int)$value;
    }
}
