<?php


namespace DMF\DeviceCookies;

use DMF\DeviceCookies\Models\FailedAttempt;
use DMF\DeviceCookies\Models\User;

interface FailedAttemptRepositoryInterface
{

    /**
     * @param FailedAttempt $failedAttempt
     * @return void
     */
    public function storeFailedAttempt(FailedAttempt $failedAttempt): void;

    /**
     * @param $deviceCookie
     * @param $getTimePeriod
     * @return int the count of the stored failed attempts of the device cookie
     */
    public function countFailedAttemptsOfDeviceCookie($deviceCookie, $getTimePeriod): int;

    /**
     * @param User $cookieUser
     * @param $getTimePeriod
     * @return int the count of the stored failed attempts of untrusted clients for the given user
     */
    public function countFailedAttemptsOfUntrustedClientsForUser(User $cookieUser, $getTimePeriod): int;

}
