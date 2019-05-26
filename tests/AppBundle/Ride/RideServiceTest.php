<?php

namespace Tests\AppBundle\Ride;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Tests\AppBundle\AppTestCase;

class RideServiceTest extends AppTestCase
{
    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function testCreateNewRide()
    {
        $passenger = $this->getRepoNewPassenger();
        $destination = $this->getHomeLocation();
        $retrievedRide = $this->getServiceNewRide($passenger, $destination);

        self::assertTrue($retrievedRide->isRiddenBy($passenger));
        self::assertTrue($retrievedRide->isLeavingFrom($destination));
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testAssignDriverToRide()
    {
        $ride = $this->getServiceNewRide(
            $this->getRepoNewPassenger(),
            $this->getHomeLocation()
        );

        $driver = $this->getRepoNewDriver();
        $this->rideService->assignDriverToRide($ride, $driver);
        $retrievedRide = $this->rideService->byId($ride->getId());

        self::assertTrue($retrievedRide->isDrivenBy($driver));
    }

    /**
     * @param AppUser $passenger
     * @param AppLocation $destination
     * @return Ride
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    protected function getServiceNewRide(
        AppUser $passenger,
        AppLocation $destination
    ): Ride {
        /** @var Ride $newRide */
        $newRide = $this->rideService->newRide(
            $passenger,
            $destination
        );

        /** @var Ride $retrievedRide */
        $retrievedRide = $this->rideService->byId($newRide->getId());

        return $retrievedRide;
    }
}
