<?php


namespace Tests\AppBundle\Production;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\ActingDriverIsNotAssignedDriverException;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\RideLifeCycleException;
use AppBundle\Exception\RideNotFoundException;
use AppBundle\Exception\UserNotFoundException;
use AppBundle\Exception\UserNotInDriverRoleException;
use AppBundle\Exception\UserNotInPassengerRoleException;
use AppBundle\Repository\RideEventRepository;
use AppBundle\Repository\RideEventRepositoryInterface;
use AppBundle\Repository\RideRepository;
use AppBundle\Repository\RideRepositoryInterface;
use AppBundle\Service\RideService;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class RideApi
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserApi
     */
    private $user;
    /**
     * @var LocationApi
     */
    private $location;

    /** @var RideRepositoryInterface  */
    private $rideRepo;

    /** @var RideEventRepositoryInterface */
    private $rideEventRepo;

    /** @var RideService  */
    private $rideService;

    public $requested;
    public $accepted;
    public $inProgress;
    public $cancelled;
    public $completed;
    public $rejected;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserApi $user,
        LocationApi $location
    ) {

        $this->entityManager = $entityManager;
        $this->user = $user;
        $this->location = $location;
        $this->rideRepo = new RideRepository($entityManager);
        $this->rideEventRepo = new RideEventRepository($entityManager);
        $this->rideService = new RideService(
            $this->rideRepo,
            $this->rideEventRepo,
            $this->location->getRepo()
        );
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function getRepoSavedRide()
    {
        $savedPassenger = $this->user->getSavedPassenger();
        $departure = $this->location->getSavedHomeLocation();
        $ride = new Ride($savedPassenger, $departure);
        $this->saveRide($ride);
        return $ride;
    }

    /**
     * @param AppUser $passenger
     * @param AppLocation $departure
     * @return Ride
     * @throws UserNotInPassengerRoleException
     */
    public function getNewRide(AppUser $passenger, AppLocation $departure)
    {
        return $this->rideService->newRide($passenger, $departure);
    }

    /**
     * @param Ride $rideInProgress
     * @param AppUser $driver
     * @return Ride
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws RideNotFoundException
     */
    public function markRideCompleted(Ride $rideInProgress, AppUser $driver)
    {
        return $this->rideService->markRideCompleted($rideInProgress, $driver);
    }

    /**
     * @param $ride
     * @return RideEventType
     * @throws RideNotFoundException
     */
    public function getRideStatus(Ride $ride)
    {
        return $this->rideService->getRideStatus($ride);
    }

    /**
     * @param Ride $acceptedRide
     * @param AppUser $driver
     * @return Ride
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws RideNotFoundException
     */
    public function markRideInProgress(Ride $acceptedRide, AppUser $driver)
    {
        return $this->rideService->markRideInProgress($acceptedRide, $driver);
    }

    /**
     * @param Ride $newRide
     * @param AppUser $driver
     * @return Ride
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws RideNotFoundException
     */
    public function acceptRide(Ride $newRide, AppUser $driver)
    {
        return $this->rideService->acceptRide($newRide, $driver);
    }

    /**
     * @return Ride
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotInPassengerRoleException
     */
    public function getSavedNewRideWithPassengerAndDestination()
    {
        $passenger = $this->user->getSavedUser();
        $this->user->makeUserPassenger($passenger);

        $departure = $this->location->getSavedHomeLocation();

        /** @var Ride $newRide */
        $newRide = $this->getNewRide(
            $passenger,
            $departure
        );

        return $newRide;
    }

    /**
     * @param AppUser $driver
     * @return Ride
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws DuplicateRoleAssignmentException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws RideNotFoundException
     */
    public function getRideInProgress(AppUser $driver)
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $acceptedRide = $this->acceptRide($newRide, $driver);
        return $this->markRideInProgress($acceptedRide, $driver);
    }

    /**
     * @return Ride
     * @throws DuplicateRoleAssignmentException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws RideNotFoundException
     */
    public function getAcceptedRide()
    {
        $newDriver = $this->user->getNewDriver();
        return $this->getAcceptedRideWithDriver($newDriver);
    }

    /**
     * @param AppUser $driver
     * @return Ride
     * @throws DuplicateRoleAssignmentException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws RideNotFoundException
     */
    public function getAcceptedRideWithDriver(AppUser $driver)
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        return $this->acceptRide($newRide, $driver);
    }

    public function getRepoLastEvent(Ride $ride)
    {
        return $this->rideEventRepo->getLastEventForRide($ride);
    }

    public function markRepoRide(Ride $ride, AppUser $passenger, RideEventType $status)
    {
        return $this->rideEventRepo->markRideStatusByActor(
            $ride,
            $passenger,
            $status
        );
    }

    /**
     * @param Uuid $id
     * @return Ride
     */
    public function getRepoRideById(Uuid $id)
    {
        return $this->rideRepo->getRideById($id);
    }

    /**
     * @return Ride
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function getRepoRideWithDestination()
    {
        $ride = $this->getRepoSavedRide();

        $this->rideRepo->assignDestinationToRide(
            $ride,
            $this->location->getWorkLocation()
        );

        /** @var Ride $retrievedRide */
        $retrievedRide = $this->getRepoRideById($ride->getId());

        return $retrievedRide;
    }

    /**
     * @param Ride $rideWithDestination
     * @param AppUser $driver
     */
    public function assignRepoDriverToRide(Ride $rideWithDestination, AppUser $driver): void
    {
        $this->rideRepo->assignDriverToRide($rideWithDestination, $driver);
    }

    /**
     * @param Uuid $rideId
     * @return Ride
     * @throws RideNotFoundException
     */
    public function getRideById(Uuid $rideId)
    {
        return $this->rideService->getRide($rideId);
    }

    /**
     * @param Ride $newRide
     * @param AppLocation $location
     * @return mixed
     */
    public function assignDestinationToRide(Ride $newRide, AppLocation $location)
    {
        $rideWithDestination = $this->rideService->assignDestinationToRide(
            $newRide,
            $location
        );

        return $rideWithDestination;
    }

    private function requested()
    {
        return $this->saveRideEventType(RideEventType::requested());
    }

    private function accepted()
    {
        return $this->saveRideEventType(RideEventType::accepted());
    }

    private function inProgress()
    {
        return $this->saveRideEventType(RideEventType::inProgress());
    }

    private function cancelled()
    {
        return $this->saveRideEventType(RideEventType::cancelled());
    }

    private function completed()
    {
        return $this->saveRideEventType(RideEventType::completed());
    }

    private function rejected()
    {
        return $this->saveRideEventType(RideEventType::rejected());
    }

    /**
     * @param RideEventType $type
     * @return RideEventType
     */
    private function saveRideEventType(RideEventType $type): RideEventType
    {
        $this->entityManager->persist($type);
        $this->entityManager->flush();
        return $type;
    }

    /**
     * @param Ride $ride
     * @return Ride
     */
    private function saveRide(Ride $ride): Ride
    {
        $this->entityManager->persist($ride);
        $this->entityManager->flush();

        return $ride;
    }

    public function bootStrapRideEventTypes(): void
    {
        $this->requested = $this->requested();
        $this->accepted = $this->accepted();
        $this->inProgress = $this->inProgress();
        $this->cancelled = $this->cancelled();
        $this->completed = $this->completed();
        $this->rejected = $this->rejected();
    }
}
