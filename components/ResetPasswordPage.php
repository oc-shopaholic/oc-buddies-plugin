<?php namespace Lovata\Buddies\Components;

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
 * Class ResetPasswordPage
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ResetPasswordPage extends Buddies {

    use ComponentTraitNotFoundResponse;

    /** @var User */
    protected $obElement;

    public function componentDetails() {
        return [
            'name'        => 'lovata.buddies::lang.component.reset_password',
            'description' => 'lovata.buddies::lang.component.reset_password_desc'
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
        if(empty($sElementSlug) || !empty($this->obUser)) {
            return $this->getErrorResponse($bDisplayError404);
        }

        //Get user ID and reset password code
        $arSlugData = explode('!', $sElementSlug);
        $iElementID = array_shift($arSlugData);
        $sPasswordCode = array_shift($arSlugData);
        if(empty($iElementID) || empty($sPasswordCode)) {
            return $this->getErrorResponse($bDisplayError404);
        }


        //Get element by slug
        /** @var User $obElement */
        $obElement = User::active()->find($iElementID);
        if(empty($obElement) || $sPasswordCode != $obElement->reset_password_code) {
            return $this->getErrorResponse($bDisplayError404);
        }

        $this->obElement = $obElement;

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

        if(empty($arUserData) || empty($this->obElement)) {
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

        //Check validation
        $obValidator = Validator::make($arUserData, $arRules, $arMessages);
        if($obValidator->fails()) {
            $arErrorData = $this->getValidationError($obValidator);
            Result::setFalse($arErrorData);
            return;
        }

        //Set new password
        $this->obElement->password_change = true;
        $this->obElement->password = $arUserData['password'];
        $this->obElement->password_confirmation = $arUserData['password'];
        $this->obElement->reset_password_code = null;
        $this->obElement->save();
    }
}