<?php

namespace DMF\DeviceCookies\Tests\Models;

use DMF\DeviceCookies\Models\FailedAttempt;
use PHPUnit\Framework\TestCase;

class FailedAttemptTest extends TestCase
{

    public function testGetUser()
    {
        $userId = 1;
        $time = time();
        $cookie = 'testcookiestring';

        $failedAttempt = new FailedAttempt($userId, $time, $cookie );

        self::assertEquals($userId, $failedAttempt->getUser());


    }

    public function testGetTime()
    {
        $user = 'test';
        $time = time();
        $cookie = 'testcookiestring';

        $failedAttempt = new FailedAttempt($user, $time, $cookie);

        self::assertEquals($time, $failedAttempt->getTime());
    }

    public function testGetCookie()
    {
        $user = 'test';
        $time = time();
        $cookie = 'testcookiestring';

        $failedAttempt = new FailedAttempt($user, $time, $cookie);

        self::assertEquals($cookie, $failedAttempt->getCookie());
    }
}
