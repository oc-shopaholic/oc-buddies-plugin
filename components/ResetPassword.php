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
 * Class ResetPassword
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ResetPassword extends Buddies
{
    use ComponentTraitNotFoundResponse;

    protected $sMode = null;

    /** @var User */
    protected $obElement;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.buddies::lang.component.reset_password',
            'description' => 'lovata.buddies::lang.component.reset_password_desc'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        $arResult = $this->getElementPageProperties();
        $arResult = array_merge($arResult, $this->getModeProperty());

        return $arResult;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function onRun()
    {
        $this->initData();

        //Check user reset password code from URL
        if(!$this->checkResetCode()) {
            return $this->getErrorResponse();
        }

        //Check component mode
        if($this->sMode != self::MODE_SUBMIT) {
            return null;
        }

        //Get user data
        $arUserData = Input::get('user');
        if(empty($arUserData)) {
            return null;
        }

        $this->resetPassword($arUserData);
        return $this->getResponseModeForm();
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
     * Ajax handler - change password
     * @return \Illuminate\Http\RedirectResponse|array
     */
    public function onChangePassword()
    {
        $this->initData();
        //Check user reset password code from URL
        if(!$this->checkResetCode()) {
            $arErrorData = [
                'message'   => Lang::get('lovata.toolbox::lang.message.e_not_correct_request'),
                'field'     => 'password',
            ];

            Result::setFalse($arErrorData);
            return $this->getResponseModeAjax();
        }

        //Get user data
        $arUserData = Input::get('user');
        $this->resetPassword($arUserData);

        return $this->getResponseModeAjax();
    }

    /**
     * Reset user password
     * @param $arUserData
     */
    protected function resetPassword($arUserData)
    {
        if(empty($arUserData) || empty($this->obElement)) {
            $arErrorData = [
                'message'   => Lang::get('lovata.toolbox::lang.message.e_not_correct_request'),
                'field'     => 'password',
            ];

            Result::setFalse($arErrorData);
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

        $arResult = [
            'message'   => Lang::get('lovata.buddies::lang.message.password_change_success'),
        ];

        Result::setTrue($arResult);
    }

    /**
     * Check user reset password code from URL
     * @return bool
     */
    protected function checkResetCode()
    {
        //Get element slug
        $sElementSlug = $this->property('slug');
        if(empty($sElementSlug) || !empty($this->obUser)) {
            return false;
        }

        //Get user ID and reset password code
        $arSlugData = explode('!', $sElementSlug);
        $iUserID = array_shift($arSlugData);
        $sPasswordCode = array_shift($arSlugData);
        if(empty($iUserID) || empty($sPasswordCode)) {
            return false;
        }

        //Get element by slug
        /** @var User $obElement */
        $obUser = User::active()->find($iUserID);
        if(empty($obUser) || $sPasswordCode != $obUser->reset_password_code) {
            return false;
        }

        $this->obElement = $obUser;
        return true;
    }
}