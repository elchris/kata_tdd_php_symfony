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
     * @var string
     */
    private $driverId;

    /**
     * RideDto constructor.
     * @param string $id
     * @param string $passengerId
     * @param float $lat
     * @param float $long
     * @param string $driverId
     */
    public function __construct(
        string $id,
        string $passengerId,
        float $lat,
        float $long,
        ?string $driverId
    ) {
        $this->id = $id;
        $this->passengerId = $passengerId;
        $this->departureLat = $lat;
        $this->departureLong = $long;
        $this->driverId = $driverId;
    }
}
