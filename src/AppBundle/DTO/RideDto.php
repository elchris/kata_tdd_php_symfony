<?php

namespace AppBundle\DTO;

class RideDto
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $passengerId;
    /**
     * @var float
     */
    private $departureLat;
    /**
     * @var float
     */
    private $departureLong;

    /**
     * RideDto constructor.
     * @param string $id
     * @param string $passengerId
     * @param float $lat
     * @param float $long
     */
    public function __construct(string $id, string $passengerId, float $lat, float $long)
    {
        $this->id = $id;
        $this->passengerId = $passengerId;
        $this->departureLat = $lat;
        $this->departureLong = $long;
    }
}
