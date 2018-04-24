<?php namespace Lovata\Buddies\Tests\Unit\Component;

use Lang;
use Lovata\Toolbox\Tests\CommonTest;

use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use Lovata\Buddies\Facades\AuthHelper;
use Lovata\Buddies\Components\UserPage;

/**
 * Class UserPageTest
 * @package Lovata\Buddies\Tests\Unit\Component
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class UserPageTest extends CommonTest
{
    /** @var  User */
    protected $obElement;

    protected $arCreateData = [
        'email'                 => 'test@test.com',
        'password'              => 123456,
        'password_confirmation' => 123456,
    ];

    /** @var  UserPage */
    protected $obComponent;

    protected $sMessage = 'Method "updateUserData" is corrupted';

    /**
     * Checking: updateUserData method
     */
    public function testUserPageMethod()
    {
        $arComponentProperty = [
            'slug_required'      => true,
            'mode'               => UserPage::MODE_AJAX,
            'flash_on'           => false,
            'redirect_on'        => false,
            'check_old_password' => true,
        ];

        $this->createTestData($arComponentProperty);

        $this->updateUserDataMethodWithEmptyData();
        $this->updateUserDataMethodWithInvalidData();
        $this->updateUserDataMethodWithValidData();
        $this->updateUserDataMethodWithLogout();
    }

    /**
     * Send empty data in updateUserData method
     */
    protected function updateUserDataMethodWithEmptyData()
    {
        //Send empty user data
        $bResult = $this->obComponent->updateUserData(null);

        self::assertEquals(false, $bResult, $this->sMessage);
        self::assertEquals(Lang::get('lovata.toolbox::lang.message.e_not_correct_request'), Result::message(), $this->sMessage);
    }

    /**
     * Send empty data in updateUserData method
     */
    protected function updateUserDataMethodWithInvalidData()
    {
        $arRequestData = [
            'email' => 'test10@test.com',
        ];

        //Send empty user data
        $bResult = $this->obComponent->updateUserData($arRequestData);

        self::assertEquals(false, $bResult, $this->sMessage);
        self::assertEquals(false, Result::status(), $this->sMessage);

        $sMessage = Lang::get('system::validation.unique',
            ['attribute' => Lang::get('lovata.toolbox::lang.field.email')]
        );
        self::assertEquals($sMessage, Result::message(), $this->sMessage);
    }

    /**
     * Send empty data in updateUserData method
     */
    protected function updateUserDataMethodWithValidData()
    {
        $arRequestData = [
            'email' => 'test12@test.com',
        ];

        //Send empty user data
        $bResult = $this->obComponent->updateUserData($arRequestData);

        self::assertEquals(true, $bResult, $this->sMessage);
        self::assertEquals(true, Result::status(), $this->sMessage);
        self::assertEquals(Lang::get('lovata.buddies::lang.message.user_update_success'), Result::message(), $this->sMessage);
    }

    /**
     * User logout + send data in updateUserData method
     */
    protected function updateUserDataMethodWithLogout()
    {
        //Logout user and try change password
        AuthHelper::logout();
        $this->obComponent->init();

        $arRequestData = [
            'email' => 'test12@test.com',
        ];

        $bResult = $this->obComponent->updateUserData($arRequestData);

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
        $arCreateData = $this->arCreateData;
        $arCreateData['email'] = 'test10@test.com';
        $this->obElement = User::create($arCreateData);

        $this->obElement = User::find($this->obElement->id);
        $this->obElement->activate();
        $this->obElement->save();

        //Create new element data
        $arCreateData = $this->arCreateData;
        $this->obElement = User::create($arCreateData);

        $this->obElement = User::find($this->obElement->id);
        $this->obElement->activate();
        $this->obElement->save();

        $arComponentProperty['slug'] = $this->obElement->id;
        AuthHelper::login($this->obElement);

        $this->obComponent = new UserPage(null, $arComponentProperty);
        $this->obComponent->init();
    }
}