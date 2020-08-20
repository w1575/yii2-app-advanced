<?php namespace frontend\tests\functional;
use frontend\tests\FunctionalTester;

class IndexPageCest
{
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function tryToTest(FunctionalTester $I)
    {
        $I->amOnPage('/site/index');
        $I->see("Привет w!");
    }
}
