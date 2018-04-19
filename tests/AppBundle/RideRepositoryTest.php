<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Repository\LocationRepository;
use AppBundle\Repository\RideRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\LocationService;
use AppBundle\Service\UserService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class RideRepositoryTest extends AppTestCase
{
    /** @var LocationService */
    private $locationService;

    /** @var UserService */
    private $userService;

    public function setUp()
    {
        parent::setUp();
        $this->userService = new UserService(new UserRepository($this->em()));
        $this->locationService = new LocationService(new LocationRepository($this->em()));
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function testCreateNewRideWithPassengerAndDeparture()
    {
        $passenger = $this->newPassenger();

        $departure = $this->locationService->getOrCreateLocation(
            37.773160,
            -122.432444
        );

        $newRide = new Ride($passenger, $departure);
        $rideRepository = new RideRepository($this->em());
        $rideRepository->saveRide($newRide);

        $retrievedRide = $rideRepository->getById($newRide->getId());

        self::assertSame($newRide->getId()->toString(), $retrievedRide->getId()->toString());
        self::assertTrue($retrievedRide->hasPassenger($passenger));
        self::assertTrue($retrievedRide->hasDeparture($departure));
    }

    /**
     * @return AppUser
     */
    private function newPassenger() : AppUser
    {
        $newUser = $this->userService->newUser('Joe', 'Passenger');
        $this->userService->makeUserPassenger($newUser);
        return $newUser;
    }
}
