<?php namespace Lovata\Buddies\Components;

use Input;
use Lang;
use Hash;
use October\Rain\Support\Collection;
use Kharanenka\Helper\Result;
use Lovata\Toolbox\Traits\Helpers\TraitComponentNotFoundResponse;

/**
 * Class ChangePassword
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ChangePassword extends Buddies
{
    use TraitComponentNotFoundResponse;

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
        if($this->sMode != self::MODE_SUBMIT) {
            return null;
        }

        //Get element slug
        $iUserID = $this->property('slug');
        if(empty($iUserID) || empty($this->obUser) || ($this->obUser->id != $iUserID)) {
            return $this->getErrorResponse();
        }

        //Get user data
        $arUserData = Input::all();
        if(empty($arUserData)) {
            return null;
        }

        $this->changePassword($arUserData);
        return $this->getResponseModeForm();
    }

    /**
     * Ajax handler - change password
     * @return \Illuminate\Http\RedirectResponse|array
     */
    public function onAjax()
    {
        $iUserID = $this->property('slug');
        if(empty($iUserID) || empty($this->obUser) || ($this->obUser->id != $iUserID)) {

            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setMessage($sMessage);
            return $this->getResponseModeAjax();
        }

        //Get user data
        $arUserData = Input::all();
        $this->changePassword($arUserData);

        return $this->getResponseModeAjax();
    }

    /**
     * Change user password
     * @param $arUserData
     *
     * @return  bool
     */
    public function changePassword($arUserData)
    {
        if(empty($arUserData) || !is_array($arUserData) || empty($this->obUser)) {

            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setMessage($sMessage);
            return false;
        }

        //Make collection
        $obUserData = Collection::make($arUserData);

        if(empty($obUserData->get('password'))) {

            $sMessage = Lang::get('system::validation.required',
                ['attribute' => Lang::get('lovata.toolbox::lang.field.password')]
            );

            Result::setFalse(['field' => 'password'])->setMessage($sMessage);
            return false;
        }

        $bCheckOldPassword = $this->property('check_old_password');
        $sOldPassword = $obUserData->get('old_password');

        if($bCheckOldPassword && !Hash::check($sOldPassword, $this->obUser->password)) {

            $sMessage = Lang::get('lovata.buddies::lang.message.e_check_old_password');
            Result::setFalse(['field' => 'password'])->setMessage($sMessage);
            return false;
        }

        //Set new password
        $this->obUser->password = $obUserData->get('password');
        $this->obUser->password_confirmation = $obUserData->get('password_confirmation');

        try {
            $this->obUser->save();
        } catch (\October\Rain\Database\ModelException $obException) {

            $this->processValidationError($obException);
            return false;
        }

        $sMessage = Lang::get('lovata.buddies::lang.message.password_change_success');
        Result::setMessage($sMessage)->setTrue();

        return true;
    }
}
