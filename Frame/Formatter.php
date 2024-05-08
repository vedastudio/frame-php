<?php

namespace Frame;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class Formatter
{
    public function phone(string $phone, string $region = "RU"): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $number_proto = $phoneUtil->parse($phone, $region);
        return $phoneUtil->format($number_proto, PhoneNumberFormat::E164);
    }
}