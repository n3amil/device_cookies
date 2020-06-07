<?php


namespace DMF\DeviceCookies\Service;


class CryptographyService
{

    public static function getHmacSignature($userId, $nonce, $secretKey): string
    {
        return hash_hmac('sha512', $userId . ',' . $nonce, $secretKey);
    }
}
