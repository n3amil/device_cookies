<?php

namespace Neamil\DeviceCookies\Tests\Models;

use Neamil\DeviceCookies\Models\LockedOutDeviceCookie;
use PHPUnit\Framework\TestCase;

class LockedOutDeviceCookieTest extends TestCase
{

    public function testGetCookie()
    {
        $cookie = 'testcookie';
        $lockoutUntil = time();
        $lockOutDeviceCookie = new LockedOutDeviceCookie($cookie, $lockoutUntil);

        self::assertEquals($cookie, $lockOutDeviceCookie->getCookie() );

    }

    public function testGetLockedUntil()
    {
        $cookie = 'testcookie';
        $lockoutUntil = time();
        $lockOutDeviceCookie = new LockedOutDeviceCookie($cookie, $lockoutUntil);

        self::assertEquals($lockoutUntil, $lockOutDeviceCookie->getLockedUntil() );

    }
}
