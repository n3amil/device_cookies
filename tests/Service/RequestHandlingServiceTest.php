<?php

namespace Neamil\DeviceCookies\Tests\Service;

use Neamil\DeviceCookies\Models\Settings;
use Neamil\DeviceCookies\Models\User;
use Neamil\DeviceCookies\Service\CookieService;
use Neamil\DeviceCookies\Service\CryptographyService;
use Neamil\DeviceCookies\Service\RequestHandlingService;
use PHPUnit\Framework\TestCase;

class RequestHandlingServiceTest extends TestCase
{

    public function testHandleAuthenticationRequestReturnsTrueIfUntrustedIsLockedOutWithInvalidCookie()
    {
        $futureTimestampFromTheYear2100 = 4102444800;
        $user = $this->buildTestUser();
        $user->setUntrustedClientsAreLockedOutUntil($futureTimestampFromTheYear2100);
        $settings = $this->buildTestSettings();
        $lockedRepo = new LockedRepo();
        $_COOKIE['foo'] ='invalid_cookie_value';

        self::assertTrue(RequestHandlingService::handleAuthenticationRequest(
            $settings, $user, $lockedRepo
        ));
    }

    public function testHandleAuthenticationRequestReturnsTrueIfUntrustedIsLockedOutWithNoCookie()
    {
        $futureTimestampFromTheYear2100 = 4102444800;
        $user = $this->buildTestUser();
        $user->setUntrustedClientsAreLockedOutUntil($futureTimestampFromTheYear2100);
        $settings = $this->buildTestSettings();
        $lockedRepo = new LockedRepo();
        unset($_COOKIE);
        self::assertTrue(RequestHandlingService::handleAuthenticationRequest(
            $settings, $user, $lockedRepo
        ));
    }

    public function testHandleAuthenticationRequestReturnsFalseIfUntrustedIsNotLockedOutWithNoCookie()
    {
        $user = $this->buildTestUser();
        $user->setUntrustedClientsAreLockedOutUntil(0);
        $settings = $this->buildTestSettings();
        $lockedRepo = new LockedRepo();
        unset($_COOKIE);
        self::assertFalse(RequestHandlingService::handleAuthenticationRequest(
            $settings, $user, $lockedRepo
        ));
    }

    public function testHandleAuthenticationRequestReturnsTrueIfCookieIsValidButLockedOut()
    {

        $user = $this->buildTestUser();
        $user->setUntrustedClientsAreLockedOutUntil(0);
        $settings = $this->buildTestSettings();
        $lockedRepo = $this->getMockBuilder(LockedRepo::class)
            ->onlyMethods(['isCookieLockedOut'])
            ->getMock();

        $lockedRepo
            ->method('isCookieLockedOut')->willReturn(true);
        $nonce = CookieService::getNonce();
        $signature = CryptographyService::getHmacSignature($user->getId(), $nonce, $settings->getSecretKey());
        $validCookie = $user->getId() . ',' . $nonce . ',' . $signature;

        $_COOKIE[$settings->getDeviceCookieName()] = $validCookie;
        self::assertTrue(RequestHandlingService::handleAuthenticationRequest(
            $settings, $user, $lockedRepo
        ));
    }

    public function testHandleAuthenticationRequestReturnsFalseIfCookieIsValidAndNotLockedOut()
    {

        $user = $this->buildTestUser();
        $user->setUntrustedClientsAreLockedOutUntil(0);
        $settings = $this->buildTestSettings();
        $lockedRepo = $this->getMockBuilder(LockedRepo::class)
            ->onlyMethods(['isCookieLockedOut'])
            ->getMock();

        $lockedRepo
            ->method('isCookieLockedOut')->willReturn(false);
        $nonce = CookieService::getNonce();
        $signature = CryptographyService::getHmacSignature($user->getId(), $nonce, $settings->getSecretKey());
        $validCookie = $user->getId() . ',' . $nonce . ',' . $signature;

        $_COOKIE[$settings->getDeviceCookieName()] = $validCookie;
        self::assertFalse(RequestHandlingService::handleAuthenticationRequest(
            $settings, $user, $lockedRepo
        ));
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

