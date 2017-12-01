<?php namespace Lovata\Buddies\Tests\Unit\Component;

use Lang;
use Lovata\Toolbox\Tests\CommonTest;

use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use Lovata\Buddies\Facades\AuthHelper;
use Lovata\Buddies\Components\RestorePassword;

/**
 * Class RestorePasswordTest
 * @package Lovata\Buddies\Tests\Unit\Component
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class RestorePasswordTest extends CommonTest
{
    /** @var  User */
    protected $obElement;

    protected $arCreateData = [
        'email'                 => 'test@test.com',
        'password'              => 123456,
        'password_confirmation' => 123456,
    ];

    /** @var  RestorePassword */
    protected $obComponent;

    protected $sMessage = 'Method "sendRestoreMail" is corrupted';

    /**
     * Checking: restorePassword method
     */
    public function testRestorePasswordMethod()
    {
        $arComponentProperty = [
            'slug_required'      => true,
            'mode'               => RestorePassword::MODE_AJAX,
            'flash_on'           => false,
            'redirect_on'        => false,
        ];

        $this->createTestData($arComponentProperty);

        $this->restorePasswordMethodWithEmptyData();
        $this->restorePasswordMethodWithInvalidEmail();
        $this->restorePasswordMethodWithValidData();
        $this->restorePasswordMethodWithLogin();
    }

    /**
     * Send empty data in restorePassword method
     */
    protected function restorePasswordMethodWithEmptyData()
    {
        //Send empty user data
        $bResult = $this->obComponent->sendRestoreMail(null);

        self::assertEquals(false, $bResult, $this->sMessage);
        self::assertEquals(Lang::get('lovata.toolbox::lang.message.e_not_correct_request'), Result::message(), $this->sMessage);

        $arRequestData = [
            'email' => '',
        ];

        $bResult = $this->obComponent->sendRestoreMail($arRequestData);

        self::assertEquals(false, $bResult, $this->sMessage);
        self::assertEquals(false, Result::status(), $this->sMessage);

        $sMessage = Lang::get('system::validation.required',
            ['attribute' => Lang::get('lovata.toolbox::lang.field.email')]
        );
        self::assertEquals($sMessage, Result::message(), $this->sMessage);
    }

    /**
     * Send empty data in restorePassword method
     */
    protected function restorePasswordMethodWithInvalidEmail()
    {
        $arRequestData = [
            'email' => 'test',
        ];

        //Send empty user data
        $bResult = $this->obComponent->sendRestoreMail($arRequestData);

        self::assertEquals(false, $bResult, $this->sMessage);
        self::assertEquals(false, Result::status(), $this->sMessage);

        $sMessage = Lang::get('lovata.buddies::lang.message.e_user_not_found',
            ['user' => $arRequestData['email']]
        );
        self::assertEquals($sMessage, Result::message(), $this->sMessage);
    }

    /**
     * Send empty data in restorePassword method
     */
    protected function restorePasswordMethodWithValidData()
    {
        $arRequestData = [
            'email' => 'test@test.com',
        ];

        //Send empty user data
        $bResult = $this->obComponent->sendRestoreMail($arRequestData);

        self::assertEquals(true, $bResult, $this->sMessage);
        self::assertEquals(true, Result::status(), $this->sMessage);
        self::assertEquals(Lang::get('lovata.buddies::lang.message.restore_mail_send_success'), Result::message(), $this->sMessage);
    }

    /**
     * User logout + send data in restorePassword method
     */
    protected function restorePasswordMethodWithLogin()
    {
        //Logout user and try change password
        AuthHelper::login($this->obElement);
        $this->obComponent->init();

        $arRequestData = [
            'email' => 'test@test.com',
        ];

        $bResult = $this->obComponent->sendRestoreMail($arRequestData);

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
        $this->obElement->save();

        AuthHelper::logout();
        $this->obComponent = new RestorePassword(null, $arComponentProperty);
        $this->obComponent->init();
    }
}
