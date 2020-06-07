<?php

namespace DMF\DeviceCookies\Tests\Models;

use DMF\DeviceCookies\Models\Settings;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{

    public function testGetSecretKey()
    {
        $timePeriod = 60;
        $maxAttemptsDuringPeriod = 5;
        $deviceCookieName = 'test';
        $cookieExpireInDays =  24;
        $secretKey = 'secrettestkey';
        $settings = new Settings($timePeriod, $maxAttemptsDuringPeriod, $deviceCookieName, $cookieExpireInDays, $secretKey);
        self::assertEquals($secretKey, $settings->getSecretKey());

    }

    public function testGetTimePeriod()
    {
        $timePeriod = 60;
        $maxAttemptsDuringPeriod = 5;
        $deviceCookieName = 'test';
        $cookieExpireInDays =  24;
        $secretKey = 'secrettestkey';
        $settings = new Settings($timePeriod, $maxAttemptsDuringPeriod, $deviceCookieName, $cookieExpireInDays, $secretKey);
        self::assertEquals($timePeriod, $settings->getTimePeriod());
    }

    public function testGetDeviceCookieName()
    {
        $timePeriod = 60;
        $maxAttemptsDuringPeriod = 5;
        $deviceCookieName = 'test';
        $cookieExpireInDays =  24;
        $secretKey = 'secrettestkey';
        $settings = new Settings($timePeriod, $maxAttemptsDuringPeriod, $deviceCookieName, $cookieExpireInDays, $secretKey);
        self::assertEquals($deviceCookieName, $settings->getDeviceCookieName());
    }

    public function testGetCookieExpireInDays()
    {
        $timePeriod = 60;
        $maxAttemptsDuringPeriod = 5;
        $deviceCookieName = 'test';
        $cookieExpireInDays =  24;
        $secretKey = 'secrettestkey';
        $settings = new Settings($timePeriod, $maxAttemptsDuringPeriod, $deviceCookieName, $cookieExpireInDays, $secretKey);
        self::assertEquals($cookieExpireInDays, $settings->getCookieExpireInDays());
    }

    public function testGetMaxAttemptsDuringPeriod()
    {
        $timePeriod = 60;
        $maxAttemptsDuringPeriod = 5;
        $deviceCookieName = 'test';
        $cookieExpireInDays =  24;
        $secretKey = 'secrettestkey';
        $settings = new Settings($timePeriod, $maxAttemptsDuringPeriod, $deviceCookieName, $cookieExpireInDays, $secretKey);
        self::assertEquals($maxAttemptsDuringPeriod, $settings->getMaxAttemptsDuringPeriod());
    }
}
