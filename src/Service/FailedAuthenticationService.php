<?php

namespace Neamil\DeviceCookies\Service;

use Neamil\DeviceCookies\FailedAttemptRepositoryInterface;
use Neamil\DeviceCookies\LockedOutDeviceCookieRepositoryInterface;
use Neamil\DeviceCookies\Models\FailedAttempt;
use Neamil\DeviceCookies\Models\LockedOutDeviceCookie;
use Neamil\DeviceCookies\Models\Settings;
use Neamil\DeviceCookies\Models\User;
use Neamil\DeviceCookies\UserRepositoryInterface;

class FailedAuthenticationService
{

    public static function registerFailedAuthenticationAttempt(User $cookieUser, FailedAttemptRepositoryInterface $failedAttemptsRepository, Settings $settings, LockedOutDeviceCookieRepositoryInterface $lockedOutDeviceCookieRepository,UserRepositoryInterface $userRepository): bool
    {
        $deviceCookieName = $settings->getDeviceCookieName();
        $requestHasDeviceCookie = CookieService::requestHasDeviceCookie($deviceCookieName);
        $cookie = self::getCookieFromRequest($requestHasDeviceCookie, $deviceCookieName);

        $failedAttempt = new FailedAttempt($cookieUser->getId(), time(), $cookie);
        $stored = $failedAttemptsRepository->storeFailedAttempt($failedAttempt);
        $cookieIsValid = self::checkIfCookieIsValid($cookieUser, $settings, $requestHasDeviceCookie, $cookie);
        self::handleCookie($cookieUser, $failedAttemptsRepository, $settings, $lockedOutDeviceCookieRepository, $userRepository, $cookieIsValid, $cookie);
        return $stored;

    }

    /**
     * @param bool $requestHasDeviceCookie
     * @param string $deviceCookieName
     * @return mixed|string
     */
    public static function getCookieFromRequest(bool $requestHasDeviceCookie, string $deviceCookieName)
    {
        $cookie = '';

        if ($requestHasDeviceCookie) {
            $cookie = CookieService::getDeviceCookieFromRequest($deviceCookieName);
        }
        return $cookie;
    }

    /**
     * @param User $cookieUser
     * @param Settings $settings
     * @param bool $requestHasDeviceCookie
     * @param string $cookie
     * @return bool
     */
    public static function checkIfCookieIsValid(User $cookieUser, Settings $settings, bool $requestHasDeviceCookie, string $cookie): bool
    {
        $cookieIsValid = false;

        if ($requestHasDeviceCookie) {
            $cookieIsValid = CookieService::validateDeviceCookie($cookie, $cookieUser, $settings->getSecretKey());
        }
        return $cookieIsValid;
    }

    /**
     * @param User $cookieUser
     * @param FailedAttemptRepositoryInterface $failedAttemptsRepository
     * @param Settings $settings
     * @param UserRepositoryInterface $userRepository
     */
    public static function handleInvalidCookie(User $cookieUser, FailedAttemptRepositoryInterface $failedAttemptsRepository, Settings $settings, UserRepositoryInterface $userRepository): void
    {
        $count = $failedAttemptsRepository->countFailedAttemptsOfUntrustedClientsForUser($cookieUser, $settings->getTimePeriod());
        if ($count > $settings->getMaxAttemptsDuringPeriod()) {
            self::handleUserLockout($cookieUser, $settings, $userRepository);
        }
    }

    /**
     * @param FailedAttemptRepositoryInterface $failedAttemptsRepository
     * @param Settings $settings
     * @param LockedOutDeviceCookieRepositoryInterface $lockedOutDeviceCookieRepository
     * @param string $cookie
     */
    public static function handleValidCookie(FailedAttemptRepositoryInterface $failedAttemptsRepository, Settings $settings, LockedOutDeviceCookieRepositoryInterface $lockedOutDeviceCookieRepository, string $cookie): void
    {
        $count = $failedAttemptsRepository->countFailedAttemptsOfDeviceCookie($cookie, $settings->getTimePeriod());
        if ($count > $settings->getMaxAttemptsDuringPeriod()) {
            self::handleLockout($settings, $lockedOutDeviceCookieRepository, $cookie);
        }
    }

    /**
     * @param User $cookieUser
     * @param FailedAttemptRepositoryInterface $failedAttemptsRepository
     * @param Settings $settings
     * @param LockedOutDeviceCookieRepositoryInterface $lockedOutDeviceCookieRepository
     * @param UserRepositoryInterface $userRepository
     * @param bool $cookieIsValid
     * @param string $cookie
     */
    public static function handleCookie(User $cookieUser, FailedAttemptRepositoryInterface $failedAttemptsRepository, Settings $settings, LockedOutDeviceCookieRepositoryInterface $lockedOutDeviceCookieRepository, UserRepositoryInterface $userRepository, bool $cookieIsValid, string $cookie): void
    {
        if ($cookieIsValid) {
            self::handleValidCookie($failedAttemptsRepository, $settings, $lockedOutDeviceCookieRepository, $cookie);
        } else {
            self::handleInvalidCookie($cookieUser, $failedAttemptsRepository, $settings, $userRepository);
        }
    }

    /**
     * @param Settings $settings
     * @param LockedOutDeviceCookieRepositoryInterface $lockedOutDeviceCookieRepository
     * @param string $cookie
     */
    public static function handleLockout(Settings $settings, LockedOutDeviceCookieRepositoryInterface $lockedOutDeviceCookieRepository, string $cookie): void
    {
        $lockedUntil = time() + $settings->getTimePeriod();
        $lockedOutDeviceCookie = new LockedOutDeviceCookie($cookie, $lockedUntil);
        $lockedOutDeviceCookieRepository->storeLockedOutCookie($lockedOutDeviceCookie);
    }

    /**
     * @param User $cookieUser
     * @param Settings $settings
     * @param UserRepositoryInterface $userRepository
     */
    public static function handleUserLockout(User $cookieUser, Settings $settings, UserRepositoryInterface $userRepository): void
    {
        $cookieUser->setUntrustedClientsAreLockedOutUntil(time() + $settings->getTimePeriod());
        $userRepository->updateUser($cookieUser);
    }
}
