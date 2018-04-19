<?php


namespace AppBundle\DTO;

class RideDto
{
    /**
     * @var string
     */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
