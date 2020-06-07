<?php


namespace DMF\DeviceCookies\Service;


use DMF\DeviceCookies\LockedOutDeviceCookieRepositoryInterface;
use DMF\DeviceCookies\Models\Settings;
use DMF\DeviceCookies\Models\User;

class RequestHandlingService
{

    public static function handleAuthenticationRequest(Settings $settings, User $user, LockedOutDeviceCookieRepositoryInterface $lockedOutDeviceCookieRepository): bool

    {
        $rejectAuthenticationAttempt = true;
        $deviceCookieName = $settings->getDeviceCookieName();

        $hasCookie = CookieService::requestHasDeviceCookie($deviceCookieName);

        if ($hasCookie) {
            $cookie = CookieService::getDeviceCookieFromRequest($deviceCookieName);
            $cookieIsValid = CookieService::validateDeviceCookie($cookie, $user, $settings->getSecretKey());
            if ($cookieIsValid === true) {
                $cookieIsLockedOut = $lockedOutDeviceCookieRepository->isCookieLockedOut($cookie);
                if ($cookieIsLockedOut === false) {
                    $rejectAuthenticationAttempt = false;
                }
            } elseif ($user->getUntrustedClientsAreLockedOutUntil() < time()){
                $rejectAuthenticationAttempt = false;
            }
        } else if ($user->getUntrustedClientsAreLockedOutUntil() < time()) {
            $rejectAuthenticationAttempt = false;
        }

        return $rejectAuthenticationAttempt;
    }

}
