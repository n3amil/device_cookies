<?php


namespace DMF\DeviceCookies;


use DMF\DeviceCookies\Models\LockedOutDeviceCookie;

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
