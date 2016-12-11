<?php namespace Lovata\Buddies\Components;

use Hash;
use Lang;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\Settings;
use Lovata\Toolbox\Classes\ComponentHelper;
use Lovata\Toolbox\Classes\ComponentTraitNotFoundResponse;
use Response;
use Lovata\Buddies\Models\User;
use Input;
use Redirect;
use Validator;

/**
 * Class ChangePasswordPage
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 * @author Denis Plisko, d.plisko@lovata.com, LOVATA Group
 */
class ChangePasswordPage extends Buddies {

    use ComponentTraitNotFoundResponse;

    /** @var User */
    protected $obElement;
    
    public function componentDetails() {
        return [
            'name'        => 'lovata.buddies::lang.component.change_password',
            'description' => 'lovata.buddies::lang.component.change_password_desc'
        ];
    }
    
    public function defineProperties() {
        
        $arResult = $this->getElementPageProperties();
        $arResult = array_merge($arResult, [
            'redirect_on' => [
                'title'             => Lang::get('lovata.buddies::lang.component.property_redirect_on'),
                'description'             => Lang::get('lovata.buddies::lang.component.property_redirect_on_desc'),
                'type'              => 'checkbox',
            ],
            'redirect_url' => [
                'title'             => Lang::get('lovata.buddies::lang.component.property_redirect_url'),
                'type'              => 'string',
            ],
            'check_old_password' => [
                'title'             => Lang::get('lovata.buddies::lang.component.property_check_old_password'),
                'description'       => Lang::get('lovata.buddies::lang.component.property_check_old_password_desc'),
                'type'              => 'checkbox',
            ],
        ]);
        
        return $arResult;
    }
    
    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function onRun() {

        $bDisplayError404 = $this->property('error_404') == 'on' ? true : false;

        //Get element slug
        $sElementSlug = $this->property('slug');
        if (empty($sElementSlug) || empty($this->obUser) || ($this->obUser->id != $sElementSlug)) {
            return $this->getErrorResponse($bDisplayError404);
        }
        
        //Get user data
        $arUserData = Input::get('user');
        if(!empty($arUserData)) {
            $this->resetPassword($arUserData);

            $bRedirectOn = $this->property('redirect_on');
            $sRedirectURL = $this->property('redirect_url');

            if(!Result::flag()) {
                return Redirect::back()->withInput()->with(Result::data());
            }

            if(!$bRedirectOn) {
                return;
            }

            if(empty($sRedirectURL)) {
                return Redirect::to('/');
            }

            return Redirect::to($sRedirectURL);
        }
        
        return;
    }

    /**
     * Reset user password
     * @param $arUserData
     */
    protected function resetPassword($arUserData) {

        if(empty($arUserData) || empty($this->obUser)) {
            return;
        }
        
        //Set validation data
        $arMessages = $this->getDefaultValidationMessage();
        $arRules = [
            'password' => 'required:create|max:255|confirmed',
            'password_confirmation' => 'required_with:password|max:255',
        ];

        $iPasswordLengthMin = Settings::getValue('password_limit_min');
        if($iPasswordLengthMin > 0) {
            $arRules['password'] = $arRules['password'].'|min:'.$iPasswordLengthMin;
        }

        $sPasswordRegexp = Settings::getValue('password_regexp');
        if(!empty($sPasswordRegexp)) {
            $arRules['password'] = $arRules['password'].'|regex:%^'.$sPasswordRegexp.'$%';
        }

        if($this->property('check_old_password')){
            Validator::extend('old_password', function ($attribute, $value, $parameters, $validator) {
                return Hash::check($value, current($parameters));
            });

            $arRules['old_password'] = 'required:password|max:255|old_password:' . $this->obUser->password;
            $arMessages['old_password.old_password'] = Lang::get('lovata.buddies::lang.message.e_check_old_password');
        }

        //Check validation
        $obValidator = Validator::make($arUserData, $arRules, $arMessages);
        if($obValidator->fails()) {
            $arErrorData = $this->getValidationError($obValidator);
            Result::setFalse($arErrorData);
            return;
        }

        //Set new password
        $this->obUser->password_change = true;
        $this->obUser->password = $arUserData['password'];
        $this->obUser->password_confirmation = $arUserData['password'];
        $this->obUser->save();
    }
}
