<?php

namespace AppBundle\DTO;

class RideDto
{
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
     * @param string $passengerId
     * @param float $lat
     * @param float $long
     */
    public function __construct(string $passengerId, float $lat, float $long)
    {
        $this->passengerId = $passengerId;
        $this->departureLat = $lat;
        $this->departureLong = $long;
    }
}
