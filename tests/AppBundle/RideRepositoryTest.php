<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\Ride;
use AppBundle\Repository\RideRepository;

/**
 * Class RideRepositoryTest
 * @package Tests\AppBundle
 *
 *
 * Ride:
 *          - Departure Location
 *          - Passenger
 *
 *          - Destination
 *          - Driver
 *
 */

class RideRepositoryTest extends AppTestCase
{
    /** @var  RideRepository */
    private $rideRepository;

    public function setUp()
    {
        parent::setUp();
        $this->rideRepository = new RideRepository($this->em());
    }

    public function testCreateRideWithDepartureAndPassenger()
    {
        $user = $this->getSavedUser();
        $this->userService->makeUserPassenger($user);

        $passenger = $this->userService->getUserById($user->getId());

        $departure = $this->locationService->getLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        $ride = new Ride($passenger, $departure);

        $this->rideRepository->save($ride);

        self::assertGreaterThan(0, $ride->getId());
    }
}
