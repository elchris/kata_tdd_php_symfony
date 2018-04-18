<?php


namespace AppBundle\DTO;

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

    public function __construct(string $id, string $first, string $last)
    {
        $this->id = $id;
        $this->first = $first;
        $this->last = $last;
    }
}
