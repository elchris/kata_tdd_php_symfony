<?php

namespace AppBundle\Dto;

class UserDto
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $first;
    /**
     * @var string
     */
    private $last;

    /**
     * UserDto constructor.
     * @param string $id
     * @param string $first
     * @param string $last
     */
    public function __construct(
        string $id,
        string $first,
        string $last
    ) {

        $this->id = $id;
        $this->first = $first;
        $this->last = $last;
    }
}
