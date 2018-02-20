<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppUser;

class FakeUser
{
    public $first;
    public $last;
    public $username;
    public $email;
    public $password;

    /**
     * FakeUser constructor.
     * @param $first
     * @param $last
     */
    public function __construct($first, $last)
    {

        $this->first = $first;
        $this->last = $last;
        $baseUsername = $first . $last;
        $this->username = $baseUsername .microtime(true);
        $this->email = $this->username.'@'.$first.$last.'.com';
        $this->password = 'password';
    }

    public function toEntity()
    {
        return new AppUser(
            $this->first,
            $this->last,
            $this->email,
            $this->username,
            $this->password
        );
    }
}
