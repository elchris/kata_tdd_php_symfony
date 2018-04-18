Kata Tasks
==========

## Tutorial Plan

### Users Basic

* UserRepositoryTest
    * Create / Get User
    * Assign Passenger Role
    
* UserServiceTest
    * Create / Get User
    
* RegisterUserCest
    * POST /register-user
        * first: 'fist name'
        * last: 'last name'
        
* Doctrine Diff & Migrate
    * users, roles, users_roles


### Users & Roles

* UserRepositoryTest
    * Assign Driver Role

* AssignRoleToUserCest
    * POST /user/{id}
        role: 'Passenger'
    * POST /user/{id}
        role: 'Driver'

* Migration
    * roles:
        1 - Passenger
        2 - Driver
 
 ### Locations & Rides
 
* LocationRepositoryTest & LocationServiceTest
    * getOrCreateLocation

* RideRepositoryTest & RideServiceTest
    * newRide($departure, $passenger)

* CreateNewRideCest
    * POST /ride
        * departure [37.773160, -122.432444]
        * passengerId

* AssignDestinationCest

* AssignDriverCest

