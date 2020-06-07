<?php


namespace DMF\DeviceCookies\Service;

use DMF\DeviceCookies\Models\Settings;
use DMF\DeviceCookies\Models\User;
use Exception;

class CookieService
{

    /**
     * Check if the incoming request contains the device cookie with the set name.
     *
     * @param string $deviceCookieName
     * @return bool Return `true` if there is device cookie. Return `false` if not.
     */
    public static function requestHasDeviceCookie($deviceCookieName): bool
    {
        $hasDeviceCookie = false;
        if (isset($_COOKIE[$deviceCookieName])) {
            $hasDeviceCookie = true;
        }
        return $hasDeviceCookie;
    }

    public static function validateDeviceCookie($cookie, User $user, $secretKey): bool
    {
        $valid = false;
        $signatureIsValid = false;
        $loginUserRepresentsTheUserTryingToAuthenticate = false;

        $userIdFromLogin = $user->getId();
        $cookieValues = explode(',', $cookie);
        if(count($cookieValues)===3){
            [$userIdFromCookie, $nonce, $signature] = $cookieValues;

            //TODO the 3 separate if statements instead of nested if statements seem to make the 3 steps of validation more clear, this can be improved for sure
            $cookieIsFormattedAsNeeded = self::checkIfCookieIsFormattedAsNeeded($userIdFromLogin, $nonce, $signature, $cookie);
            if ($cookieIsFormattedAsNeeded) {
                $signatureIsValid = self::checkIfSignatureIsValid($userIdFromLogin, $nonce, $signature, $secretKey);
            }
            if($cookieIsFormattedAsNeeded && $signatureIsValid){
                $loginUserRepresentsTheUserTryingToAuthenticate = ((int)$userIdFromCookie === $userIdFromLogin);
            }
            if($cookieIsFormattedAsNeeded && $signatureIsValid && $loginUserRepresentsTheUserTryingToAuthenticate){
                $valid = true;
            }
        }


        return $valid;
    }

    public static function getDeviceCookieFromRequest($deviceCookieName)
    {
        return $_COOKIE[$deviceCookieName];
    }

    /**
     * @param User $cookieUser
     * @param Settings $settings
     * @throws Exception
     */
    public static function issueNewDeviceCookieToUserClient(User $cookieUser, Settings $settings): void
    {
        $nonce = self::getNonce();
        $userId = $cookieUser->getId();
        $signature = CryptographyService::getHmacSignature($userId, $nonce, $settings->getSecretKey());
        self::setCookie($settings, $userId, $nonce, $signature);
    }

    public static function checkIfSignatureIsValid($userId, $nonce, $signature, $secretKey): bool
    {
        return hash_equals(
            CryptographyService::getHmacSignature($userId, $nonce, $secretKey), $signature
        );

    }

    public static function checkIfCookieIsFormattedAsNeeded(string $userNameFromLogin, $nonce, $signature, $cookie): bool
    {
        return $userNameFromLogin . ',' . $nonce . ',' . $signature === $cookie;
    }

    /**
     * @param Settings $settings
     * @param int $userId
     * @param string $nonce
     * @param string $signature
     */
    public static function setCookie(Settings $settings, int $userId, string $nonce, string $signature): void
    {
        setcookie($settings->getDeviceCookieName(), $userId . ',' . $nonce . ',' . $signature, (time() + ($settings->getCookieExpireInDays() * 24 * 60 * 60)), '/');
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function getNonce(): string
    {
        return bin2hex(random_bytes(32));
    }
}
