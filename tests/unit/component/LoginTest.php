<?php namespace Lovata\Buddies\Tests\Unit\Component;

use Lang;
use Lovata\Toolbox\Tests\CommonTest;

use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use Lovata\Buddies\Facades\AuthHelper;
use Lovata\Buddies\Components\Login;

/**
 * Class LoginTest
 * @package Lovata\Buddies\Tests\Unit\Component
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class LoginTest extends CommonTest
{
    /** @var  User */
    protected $obElement;

    protected $arCreateData = [
        'email'                 => 'test1@test.com',
        'password'              => 123456,
        'password_confirmation' => 123456,
    ];

    /** @var  Login */
    protected $obComponent;

    protected $sMessage = 'Method "login" is corrupted';

    /**
     * Checking: changePassword method
     */
    public function testLoginMethod()
    {
        $arComponentProperty = [
            'mode'               => Login::MODE_AJAX,
            'flash_on'           => false,
            'redirect_on'        => false,
        ];

        $this->createTestData($arComponentProperty);

        $this->loginWithEmptyEmail();
        $this->loginIfAuthorized();
        $this->loginWithValidData();
    }

    /**
     * Checking: authenticate with empty email
     */
    public function loginWithEmptyEmail()
    {
        $obUser = $this->obComponent->login([]);

        self::assertEquals(null, $obUser, $this->sMessage);

        self::assertEquals(false, Result::status(), $this->sMessage);
        self::assertEquals(Lang::get('lovata.toolbox::lang.message.e_not_correct_request'), Result::message(), $this->sMessage);
    }

    /**
     * Checking: authenticate, if user authorized
     */
    public function loginIfAuthorized()
    {
        $arRequestData = [
            'email' => 'test1@test.com',
            'password' => 123456
        ];

        $obUser = $this->obComponent->login($arRequestData);

        self::assertEquals($this->obElement, $obUser, $this->sMessage);

        self::assertEquals(false, Result::status(), $this->sMessage);

        AuthHelper::logout();
        $this->obComponent->init();
    }

    /**
     * Checking: authenticate with valid data
     */
    public function loginWithValidData()
    {
        $arRequestData = [
            'email'    => 'test1@test.com',
            'password' => 123456
        ];

        $obUser = $this->obComponent->login($arRequestData);

        self::assertInstanceOf(User::class, $obUser, $this->sMessage);

        self::assertEquals(true, Result::status(), $this->sMessage);
        self::assertEquals($this->obElement->id, Result::data(), $this->sMessage);
        self::assertEquals(Lang::get('lovata.buddies::lang.message.login_success'), Result::message(), $this->sMessage);
    }

    /**
     * Create brand object for test
     * @param array $arComponentProperty
     */
    protected function createTestData($arComponentProperty)
    {
        //Create new element data
        $arCreateData = $this->arCreateData;
        $arCreateData['email'] = 'test@test.com';
        AuthHelper::register($arCreateData, true);

        $arCreateData = $this->arCreateData;
        $this->obElement = AuthHelper::register($arCreateData, true);

        $arComponentProperty['slug'] = $this->obElement->id;
        AuthHelper::login($this->obElement);

        $this->obComponent = new Login(null, $arComponentProperty);
        $this->obComponent->init();
    }
}