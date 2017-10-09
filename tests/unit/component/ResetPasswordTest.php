<?php namespace Lovata\Buddies\Tests\Unit\Component;

use Lang;
use Lovata\Toolbox\Tests\CommonTest;

use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use Lovata\Buddies\Facades\AuthHelper;
use Lovata\Buddies\Components\ResetPassword;

/**
 * Class ResetPasswordTest
 * @package Lovata\Buddies\Tests\Unit\Component
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class ResetPasswordTest extends CommonTest
{
    /** @var  User */
    protected $obElement;

    protected $arCreateData = [
        'email'                 => 'test5@test.com',
        'password'              => 123456,
        'password_confirmation' => 123456,
    ];

    /** @var  ResetPassword */
    protected $obComponent;

    protected $sMessage = 'Method "resetPassword" is corrupted';

    /**
     * Checking: changePassword method
     */
    public function testResetPasswordMethod()
    {
        $arComponentProperty = [
            'slug_required'      => true,
            'mode'               => ResetPassword::MODE_AJAX,
            'flash_on'           => false,
            'redirect_on'        => false,
        ];

        $this->createTestData($arComponentProperty);

        $this->changePasswordMethodWithEmptyData();
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
        $bResult = $this->obComponent->resetPassword(null);

        self::assertEquals(false, $bResult, $this->sMessage);
        self::assertEquals(Lang::get('lovata.toolbox::lang.message.e_not_correct_request'), Result::message(), $this->sMessage);

        $arRequestData = [
            'password'              => '',
            'password_confirmation' => '',
        ];

        $bResult = $this->obComponent->resetPassword($arRequestData);

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
    protected function changePasswordMethodWithInvalidPassword()
    {
        $arRequestData = [
            'password'              => '111111',
            'password_confirmation' => '222222',
        ];

        //Send empty user data
        $bResult = $this->obComponent->resetPassword($arRequestData);

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
        $bResult = $this->obComponent->resetPassword($arRequestData);

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
        AuthHelper::login($this->obElement);
        $this->obComponent->init();

        $arRequestData = [
            'password'              => 123123,
            'password_confirmation' => 123123,
        ];

        $bResult = $this->obComponent->resetPassword($arRequestData);

        self::assertEquals(false, $bResult, $this->sMessage);
        self::assertEquals(false, Result::status(), $this->sMessage);
        self::assertEquals(Lang::get('lovata.buddies::lang.message.e_auth_fail'), Result::message(), $this->sMessage);
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
        $this->obElement->reset_password_code = 'test';
        $this->obElement->save();

        AuthHelper::logout();

        $arComponentProperty['slug'] = $this->obElement->id.'!test';
        $this->obComponent = new ResetPassword(null, $arComponentProperty);
        $this->obComponent->init();
        $this->obComponent->checkResetCode();
    }
}