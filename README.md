TDD Kata with Symfony and Doctrine
==================================

## Introduction:

* Consult ride-hailing.svg to digest some key application concepts.
* Consult Kata-Tasks.md to get an idea of the various tests you'll be writing and help shape your sequencing.
* With this said, you do not have to follow the sequencing outlined.

## Initial Set-Up

What you need:

* php 7.2
* mysql 5.7
* composer

Possible OS X Installation: (Adapt to your OS)

* ( Install Brew: https://brew.sh )
* brew unlink php@5.6 (if you already have php56)
    * You can later undo this if you wish:
        * brew unlink php@7.2
        * brew link php@5.6
* brew link php@7.2
    * or brew install php@7.2
* brew install sqlite
* brew install mysql@5.7
* brew install composer

Checkout Code:

* git clone https://github.com/elchris/kata_tdd_php_symfony.git
* cd kata_tdd_php_symfony
* switch to **clean-slate-with-acceptance** branch
    * git checkout **clean-slate-with-acceptance** 
* create new working branch from **clean-slate-with-acceptance**
    * git branch kata-run-1
    * git checkout kata-run-1

Configure DB:

* cd app/config
    * cp parameters.yml.dist parameters.yml
* mysql.server start
* log into mysql
    * create database symfony;

Run:

* cd ../..
* composer install
* vendor/bin/phpunit
* bin/console server:start
* vendor/bin/codecept run

## References

Migrations:

* Generate a single migration from the current state of the Entity Graph
    * bin/console doctrine:migrations:diff
* Execute all current migrations
    * bin/console doctrine:migrations:migrate

Generate:

* vendor/bin/codecept generate:suite api
* vendor/bin/codecept generate:cept api CreateUser

References:

* https://www.jetbrains.com/help/phpstorm/testing-with-codeception.html
* https://www.jetbrains.com/help/idea/testing-with-codeception.html
* https://laravel.com/docs/5.5/homestead#first-steps
* https://gist.github.com/diegonobre/341eb7b793fc841c0bba3f2b865b8d66

Alternatives:

* https://github.com/msgphp/user-bundle

Testing:

* Implicit:
    * http://localhost:8000/oauth/v2/auth?client_id=1_3bcbxd9e24g0gk4swg0kwgcwg4o8k8g4g888kwc44gcc0gwwk4&redirect_uri=http://localhost:8000/&response_type=token

Issues:

* https://youtrack.jetbrains.com/issue/WI-40950
* https://github.com/doctrine/doctrine2/issues/7306
