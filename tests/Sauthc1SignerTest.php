<?php


class Sauthc1SignerTest extends PHPUnit_Framework_TestCase
{

    public function testAPIAuthenticationWithSauthc1()
    {
        date_default_timezone_set('UTC');
        $date = new DateTime();
        echo $date->format('Ymd\THms\Z');
        echo "\n";
        echo $date->format('Ymd');
        echo "\n";
        echo Services_Stormpath_Util_UUID::generate(
            Services_Stormpath_Util_UUID::UUID_RANDOM,
            Services_Stormpath_Util_UUID::FMT_STRING);
    }

}
