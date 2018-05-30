<?php namespace Lovata\Buddies\Tests\Unit;

use Lang;
use Lovata\Toolbox\Tests\CommonTest;

use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use Lovata\Buddies\Facades\AuthHelper;

/**
 * Class AuthHelperTest
 * @package Lovata\Buddies\Tests\Unit
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class AuthHelperTest extends CommonTest
{
    /** @var  User */
    protected $obElement;

    protected $arCreateData = [
        'email'                 => 'test@test.com',
        'password'              => 123456,
        'password_confirmation' => 123456,
    ];

    /**
     * Checking: authenticate with empty email
     */
    public function testEmptyEmailAuth()
    {
        $sMessage = 'Method "authenticate" is corrupted';

        $obUser = AuthHelper::authenticate([]);

        self::assertEquals(null, $obUser, $sMessage);

        self::assertEquals(false, Result::status(), $sMessage);
        self::assertEquals(['field' => 'email'], Result::data(), $sMessage);

        $sMessage = Lang::get('system::validation.required',
            ['attribute' => Lang::get('lovata.toolbox::lang.field.email')]
        );
        self::assertEquals($sMessage, Result::message(), $sMessage);
    }

    /**
     * Checking: authenticate with empty password
     */
    public function testEmptyPasswordAuth()
    {
        $sMessage = 'Method "authenticate" is corrupted';

        $obUser = AuthHelper::authenticate(['email' => 'test@test.com']);

        self::assertEquals(null, $obUser, $sMessage);

        self::assertEquals(false, Result::status(), $sMessage);
        self::assertEquals(['field' => 'password'], Result::data(), $sMessage);

        $sMessage = Lang::get('system::validation.required',
            ['attribute' => Lang::get('lovata.toolbox::lang.field.password')]
        );
        self::assertEquals($sMessage, Result::message(), $sMessage);
    }

    /**
     * Checking: authenticate, if user is not activated
     */
    public function testAuthUserActivated()
    {
        $this->createTestData();

        $sMessage = 'Method "authenticate" is corrupted';

        $obUser = AuthHelper::authenticate(['email' => 'test@test.com', 'password' => 123456]);

        self::assertEquals(null, $obUser, $sMessage);

        self::assertEquals(false, Result::status(), $sMessage);
        self::assertEquals(Lang::get('lovata.buddies::lang.message.e_user_not_active'), Result::message(), $sMessage);
    }

    /**
     * Checking: login
     */
    public function testLogin()
    {
        Result::setTrue();
        $this->createTestData();

        $this->obElement->activate();
        $this->obElement->forceSave();

        $sMessage = 'Method "authenticate" is corrupted';

        $obUser = AuthHelper::authenticate(['email' => 'test@test.com', 'password' => 123456]);

        self::assertInstanceOf(User::class, $obUser, $sMessage);

        self::assertEquals($this->obElement->id, $obUser->id, $sMessage);
        self::assertEquals(true, Result::status(), $sMessage);
    }

    /**
     * Checking: authenticate with invalid password
     */
    public function testAuthThrottle()
    {
        $this->createTestData();

        $sMessage = 'Method "authenticate" is corrupted';

        AuthHelper::authenticate(['email' => 'test@test.com', 'password' => 'test']);
        AuthHelper::authenticate(['email' => 'test@test.com', 'password' => 'test']);
        AuthHelper::authenticate(['email' => 'test@test.com', 'password' => 'test']);
        AuthHelper::authenticate(['email' => 'test@test.com', 'password' => 'test']);
        $obUser = AuthHelper::authenticate(['email' => 'test@test.com', 'password' => 'test']);

        self::assertEquals(null, $obUser, $sMessage);

        self::assertEquals(false, Result::status(), $sMessage);
        self::assertEquals(['field' => 'email'], Result::data(), $sMessage);
        self::assertEquals(Lang::get('lovata.buddies::lang.message.e_login_not_correct'), Result::message(), $sMessage);

        $obUser = AuthHelper::authenticate(['email' => 'test@test.com', 'password' => 'test']);

        self::assertEquals(null, $obUser, $sMessage);

        self::assertEquals(false, Result::status(), $sMessage);
        $sMessage = Lang::get('lovata.buddies::lang.message.e_user_suspended', ['user' => $this->obElement->getLogin()]);
        self::assertEquals($sMessage, Result::message(), $sMessage);

        $obThrottle = AuthHelper::findThrottleByUserId($this->obElement->id);
        $obThrottle->unsuspend();
        $obThrottle->unban();
    }

    /**
     * Checking: register method
     */
    public function testRegister()
    {
        $sMessage = 'Method "register" is corrupted';

        $obUser = AuthHelper::register($this->arCreateData, true);

        self::assertInstanceOf(User::class, $obUser, $sMessage);
        self::assertEquals(true, $obUser->is_activated, $sMessage);

        $obUser->delete();

        $obUser = AuthHelper::register($this->arCreateData, false);

        self::assertInstanceOf(User::class, $obUser, $sMessage);
        self::assertEquals(false, $obUser->is_activated, $sMessage);
    }

    /**
     * Create brand object for test
     */
    protected function createTestData()
    {
        //Create new element data
        $arCreateData = $this->arCreateData;
        $this->obElement = User::create($arCreateData);
    }
}