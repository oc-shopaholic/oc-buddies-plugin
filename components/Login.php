<?php namespace Lovata\Buddies\Components;

use Lang;
use Input;
use Lovata\Buddies\Facades\AuthHelper;
use Kharanenka\Helper\Result;

/**
 * Class Login
 * @package Lovata\Buddies\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Login extends Buddies
{
    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.buddies::lang.component.login',
            'description' => 'lovata.buddies::lang.component.login_desc',
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
     * Auth user
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function onRun()
    {
        if ($this->sMode != self::MODE_SUBMIT) {
            return null;
        }

        $arUserData = Input::only(['email', 'password']);
        $bRemember = (bool) Input::get('remember_me', false);
        if (empty($arUserData)) {
            return null;
        }

        $this->login($arUserData, $bRemember);

        return $this->getResponseModeForm();
    }

    /**
     * Ajax auth user
     * @return \Illuminate\Http\RedirectResponse|array
     */
    public function onAjax()
    {
        $arUserData = Input::only(['email', 'password']);
        $bRemember = (bool) Input::get('remember_me', false);

        $this->login($arUserData, $bRemember);

        return $this->getResponseModeAjax();
    }

    /**
     * User auth
     * @param array $arUserData
     * @param bool  $bRemember
     * @return \Lovata\Buddies\Models\User|null
     */
    public function login($arUserData, $bRemember = false)
    {
        if (empty($arUserData) || !is_array($arUserData)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setFalse()->setMessage($sMessage);

            return null;
        }

        //Check user auth
        if (!empty($this->obUser)) {
            return $this->obUser;
        }

        $this->obUser = AuthHelper::authenticate($arUserData, $bRemember);
        if (empty($this->obUser)) {
            return null;
        }

        $sMessage = Lang::get('lovata.buddies::lang.message.login_success');
        Result::setMessage($sMessage)->setTrue($this->obUser->id);

        return $this->obUser;
    }

    /**
     * Redirect to social login page
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function onSocialiteLogin()
    {
        $sDriverCode = Input::get('driver');
        if (empty($sDriverCode) || !class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
            return null;
        }

        return \Laravel\Socialite\Facades\Socialite::driver($sDriverCode)->redirect();
    }
}

