<?php

namespace Service;

use Neamil\DeviceCookies\FailedAttemptRepositoryInterface;
use Neamil\DeviceCookies\LockedOutDeviceCookieRepositoryInterface;
use Neamil\DeviceCookies\Models\FailedAttempt;
use Neamil\DeviceCookies\Models\LockedOutDeviceCookie;
use Neamil\DeviceCookies\Models\Settings;
use Neamil\DeviceCookies\Models\User;
use Neamil\DeviceCookies\Service\CookieService;
use Neamil\DeviceCookies\Service\CryptographyService;
use Neamil\DeviceCookies\Service\FailedAuthenticationService;
use Neamil\DeviceCookies\UserRepositoryInterface;
use Exception;
use PHPUnit\Framework\TestCase;

class FailedAuthenticationServiceTest extends TestCase
{

    public function testRegisterFailedAuthenticationAttemptReturnsNothing()
    {
        $user = $this->buildTestUser();
        $failRepo = new FailedAttemptsTestRepository();
        $lockedRepo = new LockedRepo();
        $userRepo = new UserRepo();
        $settings = $this->buildSettings();

        self::assertEmpty(FailedAuthenticationService::registerFailedAuthenticationAttempt($user,$failRepo,$settings,$lockedRepo, $userRepo));
    }

    public function testGetCookieFromRequestWithInvalidCookieReturnsEmptyString()
    {
        $cookie = FailedAuthenticationService::getCookieFromRequest(false,'foo');

        self::assertSame($cookie, '');
    }

    public function testGetCookieFromRequestWithValidCookieReturnsCookieString()
    {
        $_COOKIE['foo'] = 'bar';
        $cookie = FailedAuthenticationService::getCookieFromRequest(true,'foo');

        self::assertSame($cookie, 'bar');
    }

    public function testCheckIfCookieIsValidReturnsFalseForInvalidCookie()
    {
        $user = $this->buildTestUser();
        $settings = $this->buildSettings();

        $invalidCookie = 'foooooooooooooooo';

        self::assertFalse(FailedAuthenticationService::checkIfCookieIsValid($user,$settings,true,$invalidCookie));
    }

    /**
     * @throws Exception
     */
    public function testCheckIfCookieIsValidReturnsFalseForValidCookie()
    {
        $user = $this->buildTestUser();
        $settings = $this->buildSettings();
        $nonce = CookieService::getNonce();
        $userId = $user->getId();
        $signature = CryptographyService::getHmacSignature($userId, $nonce, $settings->getSecretKey());

        $validCookie = $user->getId() . ',' . $nonce . ',' . $signature;

        self::assertTrue(FailedAuthenticationService::checkIfCookieIsValid($user,$settings,true,$validCookie));
    }

    public function testHandleInvalidCookieIfLoginCountIsHigherThanAllowedMaxAttemptsThatUntrustedClientsGetLockedOutForUser()
    {
        $failRepo = new FailedAttemptsTestRepository();
        $settings = new Settings(
            300, 0, 'foo', 180, 'bar'
        );

        $user = $this->getMockBuilder(User::class)
            ->setConstructorArgs(['test'])
            ->onlyMethods(['setUntrustedClientsAreLockedOutUntil'])
            ->getMock();

        $user->expects($this->once())
            ->method('setUntrustedClientsAreLockedOutUntil');

        $userRepo = $this->getMockBuilder(UserRepo::class)
            ->onlyMethods(['updateUser'])
            ->getMock();

        FailedAuthenticationService::handleInvalidCookie($user, $failRepo, $settings, $userRepo);

    }

    public function testHandleInvalidCookieIfLoginCountIsHigherThanAllowedMaxAttemptsThatUserIsUpdated()
    {
        $failRepo = new FailedAttemptsTestRepository();
        $settings = new Settings(
            300, 0, 'foo', 180, 'bar'
        );

        $user = $this->getMockBuilder(User::class)
            ->setConstructorArgs(['test'])
            ->onlyMethods(['setUntrustedClientsAreLockedOutUntil'])
            ->getMock();

        $userRepo = $this->getMockBuilder(UserRepo::class)
            ->onlyMethods(['updateUser'])
            ->getMock();

        $userRepo->expects($this->once())
            ->method('updateUser');

        FailedAuthenticationService::handleInvalidCookie($user, $failRepo, $settings, $userRepo);

    }

    public function testHandleValidCookieIfLoginCountIsHigherThanAllowedMaxAttemptsThatUntrustedClientsGetLockedOutForUser()
    {
        $cookie = 'foo';
        $failRepo = new FailedAttemptsTestRepository();
        $settings = new Settings(
            300, 0, 'foo', 180, 'bar'
        );


        $lockRepo = $this->getMockBuilder(LockedRepo::class)
            ->onlyMethods(['storeLockedOutCookie'])
            ->getMock();

        $lockRepo->expects($this->once())
            ->method('storeLockedOutCookie');

        FailedAuthenticationService::handleValidCookie( $failRepo, $settings, $lockRepo, $cookie );
    }

    public function testHandleCookie()
    {
        $user = $this->buildTestUser();
        $failRepo = new FailedAttemptsTestRepository();
        $lockedRepo = new LockedRepo();
        $userRepo = new UserRepo();
        $settings = $this->getMockBuilder(Settings::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMaxAttemptsDuringPeriod'])
            ->getMock();

        $settings->expects($this->once())
            ->method('getMaxAttemptsDuringPeriod');
        FailedAuthenticationService::handleCookie(
            $user, $failRepo, $settings, $lockedRepo, $userRepo, true, 'foo'
        );
    }

    public function buildTestUser()
    {
        $userId = 1;
        $user = new User('test');
        $user->setId($userId);

        return $user;

    }

    private function buildSettings()
    {
        return new Settings(
            300, 3, 'foo', 180, 'bar'
        );
    }

}

class FailedAttemptsTestRepository implements FailedAttemptRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function storeFailedAttempt(FailedAttempt $failedAttempt): void
    {
        // TODO: Implement storeFailedAttempt() method.
    }

    /**
     * @inheritDoc
     */
    public function countFailedAttemptsOfDeviceCookie($deviceCookie, $getTimePeriod): int
    {
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function countFailedAttemptsOfUntrustedClientsForUser(User $cookieUser, $getTimePeriod): int
    {
      return 1;
    }
}

class LockedRepo implements LockedOutDeviceCookieRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function isCookieLockedOut($cookie): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function storeLockedOutCookie(LockedOutDeviceCookie $cookie): void
    {
        // TODO: Implement storeLockedOutCookie() method.
    }
}

class UserRepo implements UserRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function createUserByName(string $username): ?User
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUserByName(string $name): ?User
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function updateUser(User $user): void
    {
        // TODO: Implement updateUser() method.
    }
}
