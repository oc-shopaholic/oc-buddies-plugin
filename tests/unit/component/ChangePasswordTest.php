<?php namespace Lovata\Buddies\Tests\Unit\Component;

use Lang;
use Lovata\Toolbox\Tests\CommonTest;

use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use Lovata\Buddies\Facades\AuthHelper;
use Lovata\Buddies\Components\ChangePassword;

/**
 * Class ChangePasswordTest
 * @package Lovata\Buddies\Tests\Unit\Component
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class ChangePasswordTest extends CommonTest
{
    /** @var  User */
    protected $obElement;

    protected $arCreateData = [
        'email'                 => 'test@test.com',
        'password'              => 123456,
        'password_confirmation' => 123456,
    ];

    /** @var  ChangePassword */
    protected $obComponent;

    protected $sMessage = 'Method "changePassword" is corrupted';

    /**
     * Checking: changePassword method
     */
    public function testChangePasswordMethod()
    {
        $arComponentProperty = [
            'slug_required'      => true,
            'mode'               => ChangePassword::MODE_AJAX,
            'flash_on'           => false,
            'redirect_on'        => false,
            'check_old_password' => true,
        ];

        $this->createTestData($arComponentProperty);

        $this->changePasswordMethodWithEmptyData();
        $this->changePasswordMethodWithInvalidOldPassword();
        $this->changePasswordMethodWithInvalidPassword();
        $this->changePasswordMethodWithValidData();
        $this->changePasswordMethodWithLogout();
    }

    /**
     * Send empty data in changePassword method
     */
    protected function changePasswordMethodWithEmptyData()
    {
        //Send empty user data
        $bResult = $this->obComponent->changePassword(null);

        self::assertEquals(false, $bResult, $this->sMessage);
        self::assertEquals(Lang::get('lovata.toolbox::lang.message.e_not_correct_request'), Result::message(), $this->sMessage);

        $arRequestData = [
            'password'              => '',
            'password_confirmation' => '',
        ];

        $bResult = $this->obComponent->changePassword($arRequestData);

        self::assertEquals(false, $bResult, $this->sMessage);
        self::assertEquals(false, Result::status(), $this->sMessage);

        $sMessage = Lang::get('system::validation.required',
            ['attribute' => Lang::get('lovata.toolbox::lang.field.password')]
        );
        self::assertEquals($sMessage, Result::message(), $this->sMessage);
    }

    /**
     * Send empty data in changePassword method
     */
    protected function changePasswordMethodWithInvalidOldPassword()
    {
        $arRequestData = [
            'password'              => '111111',
            'password_confirmation' => '111111',
            'old_password'          => 111111,
        ];

        //Send empty user data
        $bResult = $this->obComponent->changePassword($arRequestData);

        self::assertEquals(false, $bResult, $this->sMessage);
        self::assertEquals(false, Result::status(), $this->sMessage);
        self::assertEquals(Lang::get('lovata.buddies::lang.message.e_check_old_password'), Result::message(), $this->sMessage);

        $this->obComponent->setProperty('check_old_password', false);

        $arRequestData = [
            'password'              => '111111',
            'password_confirmation' => '111111',
            'old_password'          => 111111,
        ];

        //Send empty user data
        $bResult = $this->obComponent->changePassword($arRequestData);

        self::assertEquals(true, $bResult, $this->sMessage);
        self::assertEquals(true, Result::status(), $this->sMessage);
        self::assertEquals(Lang::get('lovata.buddies::lang.message.password_change_success'), Result::message(), $this->sMessage);
    }

    /**
     * Send empty data in changePassword method
     */
    protected function changePasswordMethodWithInvalidPassword()
    {
        $arRequestData = [
            'password'              => '111111',
            'password_confirmation' => '222222',
        ];

        //Send empty user data
        $bResult = $this->obComponent->changePassword($arRequestData);

        self::assertEquals(false, $bResult, $this->sMessage);
        self::assertEquals(false, Result::status(), $this->sMessage);

        $sMessage = Lang::get('system::validation.confirmed',
            ['attribute' => Lang::get('lovata.toolbox::lang.field.password')]
        );
        self::assertEquals($sMessage, Result::message(), $this->sMessage);
    }

    /**
     * Send empty data in changePassword method
     */
    protected function changePasswordMethodWithValidData()
    {
        $arRequestData = [
            'password'              => '111111',
            'password_confirmation' => '111111',
        ];

        //Send empty user data
        $bResult = $this->obComponent->changePassword($arRequestData);

        self::assertEquals(true, $bResult, $this->sMessage);
        self::assertEquals(true, Result::status(), $this->sMessage);
        self::assertEquals(Lang::get('lovata.buddies::lang.message.password_change_success'), Result::message(), $this->sMessage);
    }

    /**
     * User logout + send data in changePassword method
     */
    protected function changePasswordMethodWithLogout()
    {
        //Logout user and try change password
        AuthHelper::logout();
        $this->obComponent->init();

        $arRequestData = [
            'password'              => 123123,
            'password_confirmation' => 123123,
        ];

        $bResult = $this->obComponent->changePassword($arRequestData);

        self::assertEquals(false, $bResult, $this->sMessage);
        self::assertEquals(false, Result::status(), $this->sMessage);
        self::assertEquals(Lang::get('lovata.toolbox::lang.message.e_not_correct_request'), Result::message(), $this->sMessage);
    }

    /**
     * Create brand object for test
     * @param array $arComponentProperty
     */
    protected function createTestData($arComponentProperty)
    {
        //Create new element data
        $arCreateData = $this->arCreateData;
        $this->obElement = User::create($arCreateData);

        $this->obElement = User::find($this->obElement->id);
        $this->obElement->activate();
        $this->obElement->save();

        $arComponentProperty['slug'] = $this->obElement->id;
        AuthHelper::login($this->obElement);

        $this->obComponent = new ChangePassword(null, $arComponentProperty);
        $this->obComponent->init();
    }
}