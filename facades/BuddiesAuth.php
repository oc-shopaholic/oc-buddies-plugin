<?php namespace Lovata\Buddies\Facades;

use Lovata\Buddies\Models\User;
use October\Rain\Support\Facade;

/**
 * Class BuddiesAuth
 * @package Lovata\Buddies\Facades
 * @see \October\Rain\Auth\Manager
 * 
 * @method static login(User $obUser, bool $bRemember = true)
 * @method static findThrottleByLogin(string $sLoginName, string $ipAddress)
 * @method static User findUserByLogin(string $sLogin)
 * @method static findUserByCredentials(array $arCredentials)
 * @method static bool authenticate(array $arCredentials, bool $bRemember = true)
 * @method static User register(array $arUserData, $bActive = false)
 * @method static User|null getUser()
 * @method static bool check()
 * @method static logout()
 */
class BuddiesAuth extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor() { return 'buddies.auth'; }
}
