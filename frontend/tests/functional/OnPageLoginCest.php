<?php namespace frontend\tests\functional;
use frontend\tests\FunctionalTester;

class OnPageLoginCest
{
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function tryToTest(FunctionalTester $I)
    {
        $I->amOnPage("/site/login");
        $I->seeInFormFields(
            "#login-form",
            [
                'LoginForm[username]' => '',
                'LoginForm[password]' => '',
            ]
        );

    }
}
