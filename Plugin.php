<?php namespace Lovata\Buddies;

use Lang;
use App;
use Lovata\Buddies\Controllers\Users;
use Lovata\Buddies\Models\User;
use Lovata\Buddies\Models\Property;
use October\Rain\Database\Collection;
use Illuminate\Foundation\AliasLoader;
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
    
    public function registerComponents()
    {
        return [
            '\Lovata\Buddies\Components\Buddies' => 'Buddies',
            '\Lovata\Buddies\Components\Registration' => 'Registration',
            '\Lovata\Buddies\Components\RegistrationPage' => 'RegistrationPage',
            '\Lovata\Buddies\Components\LoginPage' => 'LoginPage',
            '\Lovata\Buddies\Components\RestorePasswordPage' => 'RestorePasswordPage',
            '\Lovata\Buddies\Components\Logout' => 'Logout',
            '\Lovata\Buddies\Components\ResetPasswordPage' => 'ResetPasswordPage',
            '\Lovata\Buddies\Components\ActivationPage' => 'ActivationPage',
            '\Lovata\Buddies\Components\UserPage' => 'UserPage',
            '\Lovata\Buddies\Components\ChangePasswordPage' => 'ChangePasswordPage',
        ];
    }

    public function registerSettings()
    {
        return [
            'config' => [
                'label'       => 'lovata.buddies::lang.plugin.name',
                'icon'        => 'icon-cogs',
                'description' => 'lovata.buddies::lang.plugin.description',
                'class'       => 'Lovata\Buddies\Models\Settings',
                'order'       => 100
            ]
        ];
    }

    public function register()
    {
        $alias = AliasLoader::getInstance();
        $alias->alias('BuddiesAuth', 'Lovata\Buddies\Facades\BuddiesAuth');

        App::singleton('buddies.auth', function() {
            return \Lovata\Buddies\Classes\BuddiesAuthManager::instance();
        });

    }

    public function registerMailTemplates()
    {
        return [
            'lovata.buddies::mail.restore' => Lang::get('lovata.buddies::mail.restore'),
            'lovata.buddies::mail.registration' => Lang::get('lovata.buddies::mail.registration'),
        ];
    }

    public function boot()
    {
        $this->extendUserFields();
    }

    /**
     * Extend "User" model
     */
    protected function extendUserFields() {

        Users::extendFormFields(function($form, $model, $context) {

            /** @var \Backend\Widgets\Form $form */
            /** @var User $model */

            // Only for the Product model
            if (!$model instanceof User) {
                return;
            }

            /** @var Collection $obPropertyList */
            $obPropertyList = Property::active()->orderBy('sort_order', 'asc')->get();
            if($obPropertyList->isEmpty()) {
                return;
            }

            //Get widget data for properties
            $arAdditionPropertyData = [];
            /** @var Property $obProperty */
            foreach($obPropertyList as $obProperty) {

                $arPropertyData = $obProperty->getWidgetData();
                if(!empty($arPropertyData)) {
                    $arAdditionPropertyData[Property::NAME.'['.$obProperty->code.']'] = $arPropertyData;
                }
            }

            // Add fields
            if(!empty($arAdditionPropertyData)) {
                $form->addTabFields($arAdditionPropertyData);
            }
        });
    }
}
