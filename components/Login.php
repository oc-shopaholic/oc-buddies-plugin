<?php namespace Lovata\Buddies\Components;

use Lang;
use Lovata\Buddies\Facades\BuddiesAuth;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\Settings;
use Validator;
use Input;

/**
 * Class Login
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Login extends Buddies
{
    protected $sMode = null;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.buddies::lang.component.login',
            'description' => 'lovata.buddies::lang.component.login_desc'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        $arResult = $this->getModeProperty();
        return $arResult;
    }

    /**
     * Init component data
     */
    protected function initData()
    {
        $this->sMode = $this->property('mode');
        if(empty($this->sMode)) {
            $this->sMode = self::MODE_AJAX;
        }
    }

    /**
     * Auth user
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function onRun()
    {
        $this->initData();
        if($this->sMode != self::MODE_SUBMIT) {
            return null;
        }

        $arUserData = Input::get('user');
        if(empty($arUserData)) {
            return null;
        }

        $this->login($arUserData);
        return $this->getResponseModeForm();
    }

    /**
     * Ajax auth user
     * @return \Illuminate\Http\RedirectResponse|array
     */
    public function onAjax()
    {
        $this->initData();

        $arUserData = Input::get('user');
        $this->login($arUserData);

        return $this->getResponseModeAjax();
    }

    /**
     * User auth
     * @param $arUserData
     * @return void
     */
    public function login($arUserData)
    {
        if(empty($arUserData)) {
            $arErrorData = [
                'message'   => Lang::get('lovata.toolbox::lang.message.e_not_correct_request'),
                'field'     => null,
            ];

            Result::setFalse($arErrorData);
            return;
        }

        //Check user auth
        if(!empty($this->obUser)) {
            $arErrorData = [
                'message'   => Lang::get('lovata.buddies::lang.message.e_auth_fail'),
                'field'     => null,
            ];

            Result::setFalse($arErrorData);
            return;
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
            Result::setFalse($arErrorData);
            return;
        }

        BuddiesAuth::authenticate($arUserData, true);
        if(!Result::flag()) {
            return;
        }

        $this->obUser = Result::data();
        if(empty($this->obUser)) {
            return;
        }

        $arResult = [
            'message'   => Lang::get('lovata.buddies::lang.message.login_success'),
            'user'     => $this->obUser->getData(),
        ];

        Result::setTrue($arResult);
    }
}
