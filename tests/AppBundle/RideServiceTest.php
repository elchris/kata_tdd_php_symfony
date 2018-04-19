<?php


namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Repository\LocationRepository;
use AppBundle\Repository\RideRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\LocationService;
use AppBundle\Service\RideService;
use AppBundle\Service\UserService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class RideServiceTest extends AppTestCase
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
    public function testCreateRide()
    {
        $rideService = new RideService(new RideRepository($this->em()));

        /** @var Ride $createdRide */
        $createdRide = $rideService->newRide(
            $this->newPassenger(),
            $this->locationService->getOrCreateLocation(
                37.773160,
                -122.432444
            )
        );
        self::assertNotNull($createdRide->getId());

        /** @var Ride $retrievedRide */
        $retrievedRide = $rideService->getById($createdRide->getId());

        self::assertTrue($retrievedRide->is($createdRide));
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
