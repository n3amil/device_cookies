<?php


namespace DMF\DeviceCookies\Models;


class FailedAttempt
{
    protected $user;
    protected $time;
    protected $cookie;

    /**
     * FailedAttempt constructor.
     * @param $user
     * @param $time
     * @param $cookie
     */
    public function __construct($user, $time, $cookie)
    {
        $this->user = $user;
        $this->time = $time;
        $this->cookie = $cookie;
    }

    /**
     * @return int
     */
    public function getUser(): int
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getCookie(): string
    {
        return $this->cookie;
    }

}
