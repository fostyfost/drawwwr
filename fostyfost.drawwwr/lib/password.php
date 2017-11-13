<?php

namespace FostyFost\Drawwwr;

/**
 * Class Password
 *
 * @package FostyFost\Drawwwr
 */
class Password implements Interfaces\Checkable
{
    /** @var string $password */
    private $data = '';

    /**
     * Password constructor
     *
     * @param $password
     */
    public function __construct($password)
    {
        $this->data = $password;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->data);
    }

    /**
     * @param int $size
     *
     * @return bool
     */
    public function checkLength($size = 0)
    {
        return strlen($this->data) > $size;
    }

    /**
     * @return bool|string
     */
    public function generateHash()
    {
        return password_hash($this->data, PASSWORD_DEFAULT);
    }

    /**
     * @param $password
     *
     * @return bool
     */
    public function checkHash($password)
    {
        return password_verify($password, $this->data);
    }
}
