<?php

namespace AppBundle\Dto;

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
     * @param float $departureLat
     * @param float $departureLong
     * @param string $driverId
     */
    public function __construct(
        string $id,
        string $passengerId,
        float $departureLat,
        float $departureLong,
        ?string $driverId
    ) {
        $this->id = $id;
        $this->passengerId = $passengerId;
        $this->departureLat = $departureLat;
        $this->departureLong = $departureLong;
        $this->driverId = $driverId;
    }
}
