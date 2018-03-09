<?php namespace Lovata\Buddies\Tests\Unit\Component;

use Lang;
use Lovata\Toolbox\Tests\CommonTest;

use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use Lovata\Buddies\Facades\AuthHelper;
use Lovata\Buddies\Components\Registration;

/**
 * Class RegistrationTest
 * @package Lovata\Buddies\Tests\Unit\Component
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class RegistrationTest extends CommonTest
{
    /** @var  User */
    protected $obElement;

    protected $arCreateData = [
        'email'                 => 'test1@test.com',
        'password'              => 123456,
        'password_confirmation' => 123456,
    ];

    /** @var  Registration */
    protected $obComponent;

    protected $sMessage = 'Method "registration" is corrupted';

    /**
     * Checking: changePassword method
     */
    public function testRegistrationMethod()
    {
        $arComponentProperty = [
            'mode'        => Registration::MODE_AJAX,
            'flash_on'    => false,
            'redirect_on' => false,
            'activation'  => Registration::ACTIVATION_ON,
            'force_login' => false,
        ];

        $this->createTestData($arComponentProperty);

        $this->registrationWithEmptyEmail();
        $this->registrationIfAuthorized();
        $this->registrationWithInvalidData();
        $this->registrationWithValidData();
    }

    /**
     * Checking: authenticate with empty email
     */
    public function registrationWithEmptyEmail()
    {
        $obUser = $this->obComponent->registration([]);

        self::assertEquals(null, $obUser, $this->sMessage);

        self::assertEquals(false, Result::status(), $this->sMessage);
        self::assertEquals(Lang::get('lovata.toolbox::lang.message.e_not_correct_request'), Result::message(), $this->sMessage);
    }

    /**
     * Checking: authenticate, if user authorized
     */
    public function registrationIfAuthorized()
    {
        $arRequestData = [
            'email' => 'test1@test.com',
            'password' => 123456
        ];

        $obUser = $this->obComponent->registration($arRequestData);

        self::assertEquals(null, $obUser, $this->sMessage);

        self::assertEquals(false, Result::status(), $this->sMessage);
        self::assertEquals(Lang::get('lovata.buddies::lang.message.e_auth_fail'), Result::message(), $this->sMessage);

        AuthHelper::logout();
        $this->obComponent->init();
    }

    /**
     * Checking: authenticate with invalid data
     */
    public function registrationWithInvalidData()
    {
        $arRequestData = [
            'email'    => 'test1@test.com',
            'password' => 123456
        ];

        $obUser = $this->obComponent->registration($arRequestData);

        self::assertEquals(null, $obUser, $this->sMessage);

        self::assertEquals(false, Result::status(), $this->sMessage);

        $sMessage = Lang::get('lovata.buddies::lang.message.email_is_busy', ['email' => 'test1@test.com']);
        self::assertEquals($sMessage, Result::message(), $this->sMessage);
    }

    /**
     * Checking: registration with invalid data
     */
    public function registrationWithValidData()
    {
        $arRequestData = [
            'email'                 => 'test2@test.com',
            'password'              => 123456,
            'password_confirmation' => 123456,
        ];

        $obUser = $this->obComponent->registration($arRequestData);

        self::assertInstanceOf(User::class, $obUser, $this->sMessage);

        self::assertEquals(true, $obUser->is_activated, $this->sMessage);
        self::assertEquals(true, Result::status(), $this->sMessage);
        self::assertEquals(Lang::get('lovata.buddies::lang.message.registration_success'), Result::message(), $this->sMessage);

        AuthHelper::logout();
        $this->obComponent->setProperty('activation', Registration::ACTIVATION_OFF);
        $this->obComponent->init();

        $arRequestData = [
            'email'                 => 'test3@test.com',
            'password'              => 123456,
            'password_confirmation' => 123456,
        ];

        $obUser = $this->obComponent->registration($arRequestData);

        self::assertInstanceOf(User::class, $obUser, $this->sMessage);
        self::assertEquals(false, $obUser->is_activated, $this->sMessage);
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

        $this->obComponent = new Registration(null, $arComponentProperty);
        $this->obComponent->init();
    }
}