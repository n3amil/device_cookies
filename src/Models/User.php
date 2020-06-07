<?php


namespace Neamil\DeviceCookies\Models;


class User
{
    protected $id;
    protected $name;
    protected $untrustedClientsAreLockedOutUntil;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getUntrustedClientsAreLockedOutUntil(): int
    {
        return $this->untrustedClientsAreLockedOutUntil;
    }

    /**
     * @param int $untrustedClientsAreLockedOutUntil
     */
    public function setUntrustedClientsAreLockedOutUntil(int $untrustedClientsAreLockedOutUntil): void
    {
        $this->untrustedClientsAreLockedOutUntil = $untrustedClientsAreLockedOutUntil;
    }

}
