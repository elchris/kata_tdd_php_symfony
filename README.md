TDD Kata with Symfony and Doctrine
==================================

Consult ride-hailing.svg to digest some key application concepts.

Consult Kata-Tasks.rtf to get an idea of the various tests you'll be writing and help shape your sequencing.

With this said, you do not have to follow the sequencing outlined.

Installation:

* brew install php72
* brew install mysql
* brew install composer
* brew install phpcs
* Configure IDE
** PHP Interpreter Level 7.2
** PHPUnit Run-Time
** Annotations Plugin
** Create PSR-2 Scope
** Inspection: phpcs to PSR-2 Scope
*** Exclude DoctrineMigrations
*** Exclude tests\acceptance

Run:

* mysql.server start | stop
* bin/console server:start
* vendor/bin/codecept run

Migrations:

* bin/console doctrine:migrations:diff
* bin/console doctrine:migrations:migrate

Generate:

* vendor/bin/codecept generate:suite api
* vendor/bin/codecept generate:cept api CreateUser