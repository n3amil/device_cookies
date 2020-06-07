<?php

namespace DMF\DeviceCookies\Tests\Service;

use DMF\DeviceCookies\Models\Settings;
use DMF\DeviceCookies\Models\User;
use DMF\DeviceCookies\Service\CookieService;
use DMF\DeviceCookies\Service\CryptographyService;
use Exception;
use PHPUnit\Framework\TestCase;

class CookieServiceTest extends TestCase
{

    public function testGetDeviceCookieFromRequest()
    {
        $cookiename = 'test';
        $cookievalue = 'hello';
        $_COOKIE[$cookiename] = $cookievalue;
        self::assertEquals($cookievalue, CookieService::getDeviceCookieFromRequest($cookiename));
    }

    public function testValidateDeviceCookieFails()
    {
        $cookie = 'test';
        $userId = 1;
        $secretKey = 'foobar';
        $user = new User('bla');
        $user->setId($userId);
        self::assertEquals(false, CookieService::validateDeviceCookie($cookie, $user, $secretKey));
    }

    /**
     * @throws Exception
     */
    public function testValidateDeviceCookie()
    {
        $user = $this->buildTestUser();
        $secretKey = 'foobar';
        $nonce = CookieService::getNonce();
        $signature = CryptographyService::getHmacSignature($user->getId(), $nonce, $secretKey);
        $cookie = $user->getId() . ',' . $nonce . ',' . $signature;

        self::assertEquals(true, CookieService::validateDeviceCookie($cookie, $user, $secretKey));
    }

    public function testRequestHasDeviceCookie()
    {
        $cookiename = 'test';
        $_COOKIE[$cookiename] = 'hello';
        self::assertEquals(true, CookieService::requestHasDeviceCookie($cookiename));
    }

    /**
     * @runInSeparateProcess
     * @requires xdebug
     */
    public function testSetCookieSetsACookieWithTheExpectedNonce()

    {
        $settings = $this->buildTestSettings();
        CookieService::setCookie($settings, 1, 'nice_foo_nonce', 'bar');
        $headers  =  xdebug_get_headers();
        $headerString = implode(',', $headers);
        $expected = 'Set-Cookie: ' . $settings->getDeviceCookieName() . '='. urlencode('1,nice_foo_nonce,bar');
        self::assertStringContainsString($expected, $headerString);
    }

    /**
     * @runInSeparateProcess
     * @requires xdebug
     * @throws Exception
     */
    public function testIssueNewDeviceCookieToUserClientSetsADeviceCookie()
    {
        //TODO better check for expected value
        $user = $this->buildTestUser();
        $settings = $this->buildTestSettings();
        CookieService::issueNewDeviceCookieToUserClient($user, $settings);
        $headers  =  xdebug_get_headers();
        $headerString = implode(',', $headers);
        $expected = 'Set-Cookie: foo';
        self::assertStringContainsString($expected, $headerString);
    }

    private function buildTestUser()
    {
        $userId = 1;
        $user = new User('test');
        $user->setId($userId);

        return $user;

    }

    private function buildTestSettings()
    {
        return new Settings(
            300, 3, 'foo', 180, 'bar'
        );
    }





}
