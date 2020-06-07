<?php


namespace Neamil\DeviceCookies;


use Neamil\DeviceCookies\Models\LockedOutDeviceCookie;

interface LockedOutDeviceCookieRepositoryInterface
{

    /**
     * @param $cookie
     * @return bool
     */
    public function isCookieLockedOut($cookie): bool;

    /**
     * @param LockedOutDeviceCookie $cookie
     * @return void
     */
    public function storeLockedOutCookie(LockedOutDeviceCookie $cookie): void;

}
