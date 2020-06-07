<?php


namespace Neamil\DeviceCookies\Models;


class LockedOutDeviceCookie
{
    protected $cookie;
    protected $lockedUntil;

    /**
     * LockedOutDeviceCookie constructor.
     * @param $cookie
     * @param $lockedUntil
     */
    public function __construct($cookie, $lockedUntil)
    {
        $this->cookie = $cookie;
        $this->lockedUntil = $lockedUntil;
    }

    /**
     * @return int
     */
    public function getLockedUntil(): int
    {
        return $this->lockedUntil;
    }

    /**
     * @return string
     */
    public function getCookie(): string
    {
        return $this->cookie;
    }

}
