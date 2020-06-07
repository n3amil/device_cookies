<?php


namespace DMF\DeviceCookies;

use DMF\DeviceCookies\Models\User;

interface UserRepositoryInterface
{

    /**
     * @param string $username make sure to use prepared statement for the username string, as its the user input from the login form
     * @return User
     */
    public function createUserByName(string $username): ?User;

    /**
     * @param string $name
     * @return User|null
     */
    public function getUserByName(string $name): ?User;

    /**
     * @param User $user
     */
    public function updateUser(User $user): void;
}
