<?php


namespace DMF\DeviceCookies\Models;


class Settings
{
    protected $timePeriod;
    protected $maxAttemptsDuringPeriod;
    protected $deviceCookieName;
    protected $cookieExpireInDays;
    protected $secretKey;

    public function __construct($timePeriod, $maxAttemptsDuringPeriod, $deviceCookieName, $cookieExpireInDays, $secretKey)
    {
        $this->timePeriod = $timePeriod;
        $this->maxAttemptsDuringPeriod = $maxAttemptsDuringPeriod;
        $this->deviceCookieName = $deviceCookieName;
        $this->cookieExpireInDays = $cookieExpireInDays;
        $this->secretKey = $secretKey;
    }

    /**
     * @return string
     */
    public function getDeviceCookieName(): string
    {
        return $this->deviceCookieName;
    }

    /**
     * @return mixed
     */
    public function getTimePeriod()
    {
        return $this->timePeriod;
    }

    /**
     * @return mixed
     */
    public function getMaxAttemptsDuringPeriod()
    {
        return $this->maxAttemptsDuringPeriod;
    }

    /**
     * @return mixed
     */
    public function getCookieExpireInDays()
    {
        return $this->cookieExpireInDays;
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }
}
