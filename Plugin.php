<?php namespace Lovata\Buddies;

use App;
use Lang;
use Illuminate\Foundation\AliasLoader;
use Lovata\Buddies\Classes\AuthHelperManager;
use Lovata\Buddies\Facades\AuthHelper;
use System\Classes\PluginBase;

/**
 * Class Plugin
 * @package Lovata\Buddies
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    const NAME = 'buddies';
    const CACHE_TAG = 'buddies';

    /**
     * @return array
     */
    public function registerComponents()
    {
        return [
            '\Lovata\Buddies\Components\Registration' => 'Registration',
            '\Lovata\Buddies\Components\Login' => 'Login',
            '\Lovata\Buddies\Components\Logout' => 'Logout',
            '\Lovata\Buddies\Components\ChangePassword' => 'ChangePassword',
            '\Lovata\Buddies\Components\RestorePassword' => 'RestorePassword',
            '\Lovata\Buddies\Components\ResetPassword' => 'ResetPassword',
            '\Lovata\Buddies\Components\ActivationPage' => 'ActivationPage',
            '\Lovata\Buddies\Components\UserPage' => 'UserPage',
            '\Lovata\Buddies\Components\UserData' => 'UserData',
        ];
    }

    /**
     * @return array
     */
    public function registerSettings()
    {
        return [
            'config' => [
                'label'       => 'lovata.buddies::lang.plugin.name',
                'icon'        => 'icon-cogs',
                'description' => 'lovata.buddies::lang.plugin.description',
                'class'       => 'Lovata\Buddies\Models\Settings',
                'order'       => 100,
                'permissions' => [
                    'buddies-menu-settings'
                ],
            ]
        ];
    }

    /**
     * @return array
     */
    public function registerMailTemplates()
    {
        return [
            'lovata.buddies::mail.restore' => Lang::get('lovata.buddies::mail.restore'),
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

        App::singleton('auth.helper', function() {
            return AuthHelperManager::instance();
        });
    }

    /**
     * Boot plugin method
     */
    public function boot()
    {
        $this->extendUserFields();
    }

    /**
     * Extend "User" model
     */
    protected function extendUserFields()
    {
//        Users::extendFormFields(function($form, $model, $context) {
//
//            /** @var \Backend\Widgets\Form $form */
//            /** @var User $model */
//
//            // Only for the Product model
//            if (!$model instanceof User || empty($context)) {
//                return;
//            }
//
//            /** @var Collection $obPropertyList */
//            $obPropertyList = Property::active()->orderBy('sort_order', 'asc')->get();
//            if($obPropertyList->isEmpty()) {
//                return;
//            }
//
//            //Get widget data for properties
//            $arAdditionPropertyData = [];
//            /** @var Property $obProperty */
//            foreach($obPropertyList as $obProperty) {
//
//                $arPropertyData = $obProperty->getWidgetData();
//                if(!empty($arPropertyData)) {
//                    $arAdditionPropertyData[Property::NAME.'['.$obProperty->code.']'] = $arPropertyData;
//                }
//            }
//
//            // Add fields
//            if(!empty($arAdditionPropertyData)) {
//                $form->addTabFields($arAdditionPropertyData);
//            }
//        });
    }
}
