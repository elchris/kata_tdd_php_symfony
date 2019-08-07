<?php

namespace Tests\acceptance;

use AcceptanceTester;

class FirstCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function seeHomePageHasWelcome(AcceptanceTester $I)
    {
        $I->amOnPage("/");
        $I->see('Welcome');
        $I->see('Symfony 4.3.3');
    }
}
