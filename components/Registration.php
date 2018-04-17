<?php namespace Lovata\Buddies\Components;

use Lang;
use Input;
use Kharanenka\Helper\Result;

use Lovata\Toolbox\Models\Settings;
use Lovata\Toolbox\Classes\Helper\SendMailHelper;

use Lovata\Buddies\Facades\AuthHelper;
use Lovata\Buddies\Models\User;
use Lovata\Buddies\Classes\Item\UserItem;

/**
 * Class Registration
 * @package Lovata\Buddies\Components
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Registration extends Buddies
{
    const ACTIVATION_ON = 'activation_on';
    const ACTIVATION_OFF = 'activation_off';
    const ACTIVATION_MAIL = 'activation_mail';

    const EMAIL_TEMPLATE_DATA_EVENT = 'lovata.buddies::mail.registration.template.data';

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.buddies::lang.component.registration',
            'description' => 'lovata.buddies::lang.component.registration_desc',
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        $arResult = $this->getModeProperty();
        $arResult['activation'] = [
            'title'   => 'lovata.buddies::lang.component.property_activation',
            'type'    => 'dropdown',
            'options' => [
                self::ACTIVATION_OFF  => Lang::get('lovata.buddies::lang.component.property_'.self::ACTIVATION_OFF),
                self::ACTIVATION_ON   => Lang::get('lovata.buddies::lang.component.property_'.self::ACTIVATION_ON),
                self::ACTIVATION_MAIL => Lang::get('lovata.buddies::lang.component.property_'.self::ACTIVATION_MAIL),
            ],
        ];

        $arResult['force_login'] = [
            'title' => 'lovata.buddies::lang.component.property_force_login',
            'type'  => 'checkbox',
        ];

        return $arResult;
    }

    /**
     * Registration (mode = form)
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function onRun()
    {
        if ($this->sMode != self::MODE_SUBMIT) {
            return null;
        }

        $arUserData = Input::all();
        if (empty($arUserData)) {
            return null;
        }

        $this->registration($arUserData);

        return $this->getResponseModeForm();
    }

    /**
     * Registration (ajax request)
     * @return \Illuminate\Http\RedirectResponse|array
     */
    public function onAjax()
    {
        //Get user data
        $arUserData = Input::all();
        $this->registration($arUserData);

        return $this->getResponseModeAjax();
    }

    /**
     * Check: email is available
     * @return  array
     */
    public function onCheckEmail()
    {
        //Get user email
        $sEmail = Input::get('email');
        $this->checkAvailabilityEmail($sEmail);

        return response(Result::get());
    }

    /**
     * User registration
     * @param array $arUserData
     * @return User|null
     */
    public function registration($arUserData)
    {
        if (empty($arUserData) || !is_array($arUserData)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setFalse()->setMessage($sMessage);

            return null;
        }

        //Check user auth
        if (!empty($this->obUser)) {
            $sMessage = Lang::get('lovata.buddies::lang.message.e_auth_fail');
            Result::setFalse()->setMessage($sMessage);

            return null;
        }

        //Check email is busy or available
        if (isset($arUserData['email'])) {
            $this->checkAvailabilityEmail($arUserData['email']);
            if (!Result::status()) {
                return null;
            }
        }

        $sActivationType = $this->property('activation');
        $bUserActivate = $sActivationType == self::ACTIVATION_ON;

        try {
            //Create new user
            $obUser = AuthHelper::register($arUserData, $bUserActivate);
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);

            return null;
        }

        if (empty($obUser)) {
            $sMessage = Lang::get('lovata.buddies::lang.message.e_user_create');
            Result::setFalse()->setMessage($sMessage);

            return null;
        }

        //User activation
        $this->afterRegistrationActivate($obUser);

        if ($this->property('force_login')) {
            AuthHelper::login($obUser);
        }

        $this->obUser = $obUser;

        $sMessage = Lang::get('lovata.buddies::lang.message.registration_success');
        Result::setMessage($sMessage)->setTrue($obUser->id);

        return $obUser;
    }

    /**
     * Check: email is available
     * @param string $sEmail
     */
    protected function checkAvailabilityEmail($sEmail)
    {
        if (empty($sEmail)) {
            return;
        }

        $sMessage = Lang::get('lovata.buddies::lang.message.email_is_available', ['email' => $sEmail]);
        Result::setMessage($sMessage)->setTrue();

        //Get user by email
        $obUser = User::getByEmail($sEmail)->first();
        if (!empty($obUser)) {
            $sMessage = Lang::get('lovata.buddies::lang.message.email_is_busy', ['email' => $sEmail]);
            Result::setFalse()->setMessage($sMessage);
        }

        return;
    }

    /**
     * Activate user after registration
     * @param User $obUser
     */
    protected function afterRegistrationActivate(&$obUser)
    {
        if (empty($obUser)) {
            return;
        }

        $sActivationType = $this->property('activation');
        switch ($sActivationType) {
            case self::ACTIVATION_ON:
                break;
            case self::ACTIVATION_OFF:
                $obUser->activation_code = $obUser->getActivationCode();
                $obUser->is_activated = false;
                break;
            case self::ACTIVATION_MAIL:

                $obUser->activation_code = $obUser->getActivationCode();
                $obUser->is_activated = false;

                //Get mail data
                $arMailData = [
                    'user'      => $obUser,
                    'user_item' => UserItem::make($obUser->id, $obUser),
                    'site_url'  => config('app.url'),
                ];

                $sTemplateName = Settings::getValue('registration_mail_template', 'lovata.buddies::mail.registration');

                $obSendMailHelper = SendMailHelper::instance();
                $obSendMailHelper->send(
                    $sTemplateName,
                    $obUser->email,
                    $arMailData,
                    self::EMAIL_TEMPLATE_DATA_EVENT,
                    true);

                break;
        }

        $obUser->forceSave();
    }
}

