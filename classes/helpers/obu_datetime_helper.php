<?php
namespace enrol_ethos\helpers;


class obu_datetime_helper
{
    public static function convertStringToTimeStamp(string $datetimeStr) : int {
        return strtotime($datetimeStr);
    }
}
