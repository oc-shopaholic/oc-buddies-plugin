<?php namespace Lovata\Buddies\Components;

use Lang;
use Lovata\Buddies\Facades\BuddiesAuth;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\Settings;
use System\Classes\PluginManager;
use Validator;
use Input;
use Redirect;

/**
 * Class LoginPage
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class LoginPage extends Login  {

    public function componentDetails() {
        return [
            'name'        => 'lovata.buddies::lang.component.login_page',
            'description' => 'lovata.buddies::lang.component.login_page_desc'
        ];
    }

    public function defineProperties() {
        return [
            'redirect_url' => [
                'title'             => Lang::get('lovata.buddies::lang.component.property_redirect_url'),
                'type'              => 'string',
            ],
        ];
    }
    
    /**
     * Auth user
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function onRun()
    {
        $arUserData = Input::get('user');
        if(empty($arUserData)) {
            return;
        }

        //Check user auth
        if(!empty($this->obUser)) {
            
            $arErrorData = [
                'message'   => Lang::get('lovata.buddies::lang.message.e_auth_fail'),
                'field'     => null,
            ];

            return Redirect::back()->withInput()->with($arErrorData);
        }
        
        //Get validation data
        $arMessages = $this->getDefaultValidationMessage();
        $arRules = [
            'email' => 'required|email',
            'password' => 'required|max:255',
        ];

        $iPasswordLengthMin = Settings::getValue('password_limit_min');
        if($iPasswordLengthMin > 0) {
            $arRules['password'] = $arRules['password'].'|min:'.$iPasswordLengthMin;
        }

        $sPasswordRegexp = Settings::getValue('password_regexp');
        if(!empty($sPasswordRegexp)) {
            $arRules['password'] = $arRules['password'].'|regex:%^'.$sPasswordRegexp.'$%';
        }

        //Validation user data
        $obValidator = Validator::make($arUserData, $arRules, $arMessages);
        if($obValidator->fails()) {
            
            $arErrorData = $this->getValidationError($obValidator);
            return Redirect::back()->withInput()->with($arErrorData);
        }
        
        BuddiesAuth::authenticate($arUserData, true);
        if(!Result::flag()) {
            return Redirect::back()->withInput()->with(Result::data());
        }

        if(PluginManager::instance()->hasPlugin('Lovata.CustomBuddies')) {
            $sCustomRedirectURL = \Lovata\CustomBuddies\Classes\LoginExtend::getRedirectURL(Result::data());
            if(!empty($sCustomRedirectURL)) {
                return Redirect::to($sCustomRedirectURL);
            }
        }
        
        $sRedirectURL = $this->property('redirect_url');
        if(empty($sRedirectURL)) {
            return Redirect::to('/');
        }

        return Redirect::to($sRedirectURL);
    }
}
