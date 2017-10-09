<?php namespace Lovata\Buddies\Facades;

use October\Rain\Support\Facade;

/**
 * Class AuthHelper
 * @package Lovata\Buddies\Facades
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Auth\Manager
 * @mixin \Lovata\Buddies\Classes\AuthHelperManager
 *
 * @method static \Lovata\Buddies\Models\User authenticate(array $arLoginData, $bRemember = false)
 * @method static login($obUser, $bRemember = false)
 * @method static \Lovata\Buddies\Models\User register(array $arCredentials, $bActive = false)
 * @method static \Lovata\Buddies\Models\User|null getUser()
 * @method static bool check()
 * @method static logout()
 */
class AuthHelper extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth.helper';
    }
}
