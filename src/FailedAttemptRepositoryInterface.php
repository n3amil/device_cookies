<?php


namespace Neamil\DeviceCookies;

use Neamil\DeviceCookies\Models\FailedAttempt;
use Neamil\DeviceCookies\Models\User;

interface FailedAttemptRepositoryInterface
{

    /**
     * @param FailedAttempt $failedAttempt
     * @return bool return true on successful storing and false if something goes wrong
     */
    public function storeFailedAttempt(FailedAttempt $failedAttempt): bool ;

    /**
     * @param $deviceCookie
     * @param $getTimePeriod
     * @return int the count of the stored failed attempts of the device cookie
     */
    public function countFailedAttemptsOfDeviceCookie(string $deviceCookie, $getTimePeriod): int;

    /**
     * @param User $cookieUser
     * @param $getTimePeriod
     * @return int the count of the stored failed attempts of untrusted clients for the given user
     */
    public function countFailedAttemptsOfUntrustedClientsForUser(User $cookieUser, $getTimePeriod): int;

}
