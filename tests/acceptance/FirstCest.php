<?php

namespace Tests\acceptance;

use AcceptanceTester;

class FirstCest
{
    public function _before(AcceptanceTester $I): void
    {
    }

    public function _after(AcceptanceTester $I): void
    {
    }

    // tests
    public function seeHomePageHasWelcome(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->see('Welcome');
        $I->see('Symfony 3.4.27');
    }
}
