TDD Kata with Symfony and Doctrine
==================================

Introduction:

* Consult ride-hailing.svg to digest some key application concepts.
* Consult Kata-Tasks.rtf to get an idea of the various tests you'll be writing and help shape your sequencing.
* With this said, you do not have to follow the sequencing outlined.

Installation:

* Install Brew: https://brew.sh
* brew install php72
* brew install php72-xdebug
* brew install php72-yaml
* brew install sqlite
* brew install mysql
* brew install composer
* brew install php-code-sniffer

Checkout Code:

* https://github.com/elchris/kata_tdd_php_symfony
* switch to clean-slate branch
* create new working branch from clean-slate
* composer install

Configure IDE:

* PHP Interpreter Level 7.2
* PHPUnit Run-Time
* Annotations Plugin
* Create PSR-2 Scope
* Inspection: phpcs to PSR-2 Scope
    * Exclude DoctrineMigrations
    * Exclude tests\acceptance
    
Configure DB

* cp parameters.yml.dist parameters.yml
* create database symfony;

Run:

* vendor/bin/phpunit
* mysql.server start | stop
* bin/console server:start
* vendor/bin/codecept run

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

Issues:

* https://youtrack.jetbrains.com/issue/WI-40950


Stats:
* [![](http://codescene.io/projects/2090/status.svg) Get more details at **codescene.io**.](http://codescene.io/projects/2090/jobs/latest-successful/results)
