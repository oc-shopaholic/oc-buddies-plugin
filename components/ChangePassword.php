<?php namespace Lovata\Buddies\Components;

use Input;
use Lang;
use Hash;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\Settings;
use Validator;
use Lovata\Toolbox\Classes\ComponentTraitNotFoundResponse;

/**
 * Class ChangePassword
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ChangePassword extends Buddies
{
    use ComponentTraitNotFoundResponse;

    protected $sMode = null;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.buddies::lang.component.change_password',
            'description' => 'lovata.buddies::lang.component.change_password_desc'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        $arResult = $this->getElementPageProperties();
        $arResult = array_merge($arResult, $this->getModeProperty());

        $arResult['check_old_password'] = [
            'title'             => 'lovata.buddies::lang.component.property_check_old_password',
            'type'              => 'checkbox',
        ];

        return $arResult;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|null
     */
    public function onRun()
    {
        $this->initData();
        if($this->sMode != self::MODE_SUBMIT) {
            return null;
        }

        //Get element slug
        $iUserID = $this->property('slug');
        if(empty($iUserID) || empty($this->obUser) || ($this->obUser->id != $iUserID)) {
            return $this->getErrorResponse();
        }

        //Get user data
        $arUserData = Input::get('user');
        if(empty($arUserData)) {
            return null;
        }

        $this->changePassword($arUserData);
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
    public function onAjax()
    {
        $this->initData();

        $iUserID = $this->property('slug');
        if(empty($iUserID) || empty($this->obUser) || ($this->obUser->id != $iUserID)) {
            $arErrorData = [
                'message'   => Lang::get('lovata.toolbox::lang.message.e_not_correct_request'),
                'field'     => 'password',
            ];

            Result::setFalse($arErrorData);
            return $this->getResponseModeAjax();
        }

        //Get user data
        $arUserData = Input::get('user');
        $this->changePassword($arUserData);

        return $this->getResponseModeAjax();
    }

    /**
     * Change user password
     * @param $arUserData
     */
    protected function changePassword($arUserData)
    {
        if(empty($arUserData) || empty($this->obUser)) {
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
            'password'                  => 'required:create|max:255|confirmed',
            'password_confirmation'     => 'required_with:password|max:255',
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

        if($this->property('check_old_password') && !Hash::check($arUserData['old_password'], $this->obUser->password)) {

            $arErrorData = [
                'message'   => Lang::get('lovata.buddies::lang.message.e_check_old_password'),
                'field'     => 'password',
            ];

            Result::setFalse($arErrorData);
            return;
        }

        //Set new password
        $this->obUser->password_change = true;
        $this->obUser->password = $arUserData['password'];
        $this->obUser->password_confirmation = $arUserData['password'];
        $this->obUser->save();

        $arResult = [
            'message'   => Lang::get('lovata.buddies::lang.message.password_change_success'),
        ];

        Result::setTrue($arResult);
    }
}
