<?php namespace Lovata\Buddies;

use App;
use Lang;
use Event;
use Illuminate\Foundation\AliasLoader;
use Lovata\Buddies\Classes\AuthHelperManager;
use Lovata\Buddies\Classes\Event\ExtendFieldHandler;
use Lovata\Buddies\Classes\Event\UserModelHandler;
use System\Classes\PluginBase;

/**
 * Class Plugin
 * @package Lovata\Buddies
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    /** @var array Plugin dependencies */
    public $require = ['Lovata.Toolbox'];

    /**
     * @return array
     */
    public function registerComponents()
    {
        return [
            '\Lovata\Buddies\Components\Registration'    => 'Registration',
            '\Lovata\Buddies\Components\Login'           => 'Login',
            '\Lovata\Buddies\Components\SocialiteLogin'  => 'SocialiteLogin',
            '\Lovata\Buddies\Components\Logout'          => 'Logout',
            '\Lovata\Buddies\Components\ChangePassword'  => 'ChangePassword',
            '\Lovata\Buddies\Components\RestorePassword' => 'RestorePassword',
            '\Lovata\Buddies\Components\ResetPassword'   => 'ResetPassword',
            '\Lovata\Buddies\Components\ActivationPage'  => 'ActivationPage',
            '\Lovata\Buddies\Components\UserPage'        => 'UserPage',
            '\Lovata\Buddies\Components\UserData'        => 'UserData',
        ];
    }

    /**
     * @return array
     */
    public function registerMailTemplates()
    {
        return [
            'lovata.buddies::mail.restore'      => Lang::get('lovata.buddies::mail.restore'),
            'lovata.buddies::mail.registration' => Lang::get('lovata.buddies::mail.registration'),
        ];
    }

    /**
     * Register method of plugin
     */
    public function register()
    {
        $obAlias = AliasLoader::getInstance();
        $obAlias->alias('AuthHelper', 'Lovata\Buddies\Facades\AuthHelper');

        App::singleton('auth.helper', function () {
            return AuthHelperManager::instance();
        });
    }

    /**
     * Boot plugin method
     */
    public function boot()
    {
        Event::subscribe(ExtendFieldHandler::class);
        Event::subscribe(UserModelHandler::class);
    }
}
