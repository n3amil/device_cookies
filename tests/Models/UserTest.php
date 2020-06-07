<?php

namespace DMF\DeviceCookies\Tests\Models;

use DMF\DeviceCookies\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{

    public function testSetGetId()
    {
        $username = 'test';
        $id = 1;
        $user = new User($username);
        $user->setId($id);
        self::assertEquals($id, $user->getId());

    }

    public function testSetGetName()
    {
        $username = 'test';
        $username_new = 'new';
        $user = new User($username);
        $user->setName($username_new);
        self::assertEquals($username_new, $user->getName());
    }

    public function testSetGetUntrustedClientsAreLockedOutUntil()
    {
        $username = 'test';
        $lockedUntil = time();
        $user = new User($username);
        $user->setUntrustedClientsAreLockedOutUntil( $lockedUntil);
        self::assertEquals($lockedUntil, $user->getUntrustedClientsAreLockedOutUntil());
    }
}
