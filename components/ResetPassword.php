<?php namespace Lovata\Buddies\Components;

use Lang;
use Input;
use Kharanenka\Helper\Result;
use October\Rain\Support\Collection;
use Lovata\Buddies\Models\User;
use Lovata\Toolbox\Traits\Helpers\TraitComponentNotFoundResponse;

/**
 * Class ResetPassword
 * @package Lovata\Buddies\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ResetPassword extends Buddies
{
    use TraitComponentNotFoundResponse;

    /** @var  User */
    protected $obElement;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.buddies::lang.component.reset_password',
            'description' => 'lovata.buddies::lang.component.reset_password_desc',
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
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|null
     */
    public function onRun()
    {
        //Check user reset password code from URL
        if (!$this->checkResetCode()) {
            return $this->getErrorResponse();
        }

        //Check component mode
        if ($this->sMode != self::MODE_SUBMIT) {
            return null;
        }

        //Get user data
        $arUserData = Input::only(['password', 'password_confirmation']);
        if (empty($arUserData)) {
            return null;
        }

        $this->resetPassword($arUserData);

        return $this->getResponseModeForm();
    }

    /**
     * Ajax handler - change password
     * @return \Illuminate\Http\RedirectResponse|array
     */
    public function onAjax()
    {
        //Check user reset password code from URL
        if (!$this->checkResetCode()) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setFalse()->setMessage($sMessage);

            return $this->getResponseModeAjax();
        }

        //Get user data
        $arUserData = Input::only(['password', 'password_confirmation']);
        $this->resetPassword($arUserData);

        return $this->getResponseModeAjax();
    }

    /**
     * Reset user password
     * @param array $arUserData
     *
     * @return bool
     */
    public function resetPassword($arUserData)
    {
        if (empty($arUserData) || !is_array($arUserData) || empty($this->obElement)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setFalse()->setMessage($sMessage);

            return false;
        }

        //Check user auth
        if (!empty($this->obUser)) {
            $sMessage = Lang::get('lovata.buddies::lang.message.e_auth_fail');
            Result::setFalse()->setMessage($sMessage);

            return null;
        }

        //Make collection
        $obUserData = Collection::make($arUserData);
        if (empty($obUserData->get('password'))) {
            $sMessage = Lang::get(
                'system::validation.required',
                ['attribute' => Lang::get('lovata.toolbox::lang.field.password')]
            );

            Result::setFalse(['field' => 'password'])->setMessage($sMessage);

            return false;
        }

        //Set new password
        $this->obElement->password = $obUserData->get('password');
        $this->obElement->password_confirmation = $obUserData->get('password_confirmation');
        $this->obElement->reset_password_code = null;

        try {
            $this->obElement->save();
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);

            return false;
        }

        $sMessage = Lang::get('lovata.buddies::lang.message.password_change_success');
        Result::setMessage($sMessage)->setTrue();

        return true;
    }

    /**
     * Check user reset password code from URL
     * @return bool
     */
    public function checkResetCode()
    {
        //Get element slug
        $sElementSlug = $this->property('slug');
        if (empty($sElementSlug) || !empty($this->obUser)) {
            return false;
        }

        //Get user ID and reset password code
        $arSlugData = explode('!', $sElementSlug);
        $iUserID = array_shift($arSlugData);
        $sPasswordCode = array_shift($arSlugData);
        if (empty($iUserID) || empty($sPasswordCode)) {
            return false;
        }

        //Get element by slug
        /** @var User $obUser */
        $obUser = User::active()->find($iUserID);
        if (empty($obUser) || $sPasswordCode != $obUser->reset_password_code) {
            return false;
        }

        $this->obElement = $obUser;

        return true;
    }
}
