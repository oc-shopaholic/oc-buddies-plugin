<?php namespace Lovata\Buddies\Components;

use Lovata\Buddies\Models\Settings;
use Lang;
use Lovata\Buddies\Facades\BuddiesAuth;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use System\Classes\PluginManager;
use Validator;
use Input;
use Mail;

/**
 * Class Registration
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Registration extends Buddies
{
    const ACTIVATION_ON = 'activation_on';
    const ACTIVATION_OFF = 'activation_off';
    const ACTIVATION_MAIL = 'activation_mail';

    protected $sMode = null;

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
            'title'             => 'lovata.buddies::lang.component.property_activation',
            'type'              => 'dropdown',
            'options'           => [
                self::ACTIVATION_OFF    => Lang::get('lovata.buddies::lang.component.property_'.self::ACTIVATION_OFF),
                self::ACTIVATION_ON     => Lang::get('lovata.buddies::lang.component.property_'.self::ACTIVATION_ON),
                self::ACTIVATION_MAIL   => Lang::get('lovata.buddies::lang.component.property_'.self::ACTIVATION_MAIL),
            ]
        ];

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
     * Registration (mode = form)
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

        $this->registration($arUserData);
        return $this->getResponseModeForm();
    }

    /**
     * Registration (ajax request)
     * @return \Illuminate\Http\RedirectResponse|array
     */
    public function onAjax()
    {
        $this->initData();

        //Get user data
        $arUserData = Input::get('user');
        $this->registration($arUserData);

        return $this->getResponseModeAjax();
    }

    /**
     * User registration
     * @param $arUserData
     * @return void
     */
    protected function registration($arUserData)
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

        $arMessages = $this->getDefaultValidationMessage();
        $arMessages['email.unique'] = Lang::get('lovata.buddies::lang.message.e_email_unique');

        //Default validation 
        $obValidator = Validator::make($arUserData, User::getValidationRules(), $arMessages);

        if($obValidator->fails()) {
            $arErrorData = $this->getValidationError($obValidator);
            Result::setFalse($arErrorData);
            return;
        }

        //Custom validation
        if(PluginManager::instance()->hasPlugin('Lovata.CustomBuddies')) {
            \Lovata\CustomBuddies\Classes\RegistrationExtend::extendValidation($arUserData);
            if(!Result::flag()) {
                return;
            }
        }

        //Create new user
        /** @var User $obUser */
        $obUser = BuddiesAuth::register($arUserData, true);
        if(empty($obUser)) {

            $arErrorData = [
                'message'   => Lang::get('lovata.buddies::lang.message.e_user_create'),
                'field'     => null,
            ];

            Result::setFalse($arErrorData);
            return;
        }

        //User activation
        $this->afterRegistrationActivate($obUser);
        
        if($this->property('force_login')) {
            BuddiesAuth::login($obUser);
        }

        $this->obUser = $obUser;

        $arResult = [
            'message'   => Lang::get('lovata.buddies::lang.message.registration_success'),
            'user'     => $obUser->getData(),
        ];

        Result::setTrue($arResult);
    }

    /**
     * Activate user after registration
     * @param User $obUser
     */
    protected function afterRegistrationActivate(&$obUser)
    {
        if(empty($obUser)) {
            return;
        }
        
        $iActivationType = $this->property('activation');
        switch($iActivationType) {
            case self::ACTIVATION_ON:
                $obUser->activation_code = null;
                $obUser->is_activated = true;
                $obUser->activated_at = $obUser->freshTimestamp();
                break;
            case self::ACTIVATION_OFF:
                $obUser->activation_code = $obUser->getActivationCode();
                $obUser->is_activated = false;
                break;
            case self::ACTIVATION_MAIL:
                
                $obUser->activation_code = $obUser->getActivationCode();
                $obUser->is_activated = false;
                
                //Get user mail data
                $arMailData = [
                    'obUser' => $obUser,
                    'site_url' => env('SITE_URL'),
                ];
                
                $sUserEmail = $obUser->email;
                
                //Get queue settings
                $bUseQueue = Settings::getValue('queue_on');
                $sQueueName = Settings::getValue('queue_name');
                
                //Send registration mail
                if($bUseQueue && empty($sQueueName)) {
                    Mail::queue('lovata.buddies::mail.registration', $arMailData, function($obMessage) use ($sUserEmail) {
                        $obMessage->to($sUserEmail);
                    });
                    
                } else if ($bUseQueue && !empty($sQueueName)) {
                    Mail::queueOn($sQueueName, 'lovata.buddies::mail.registration', $arMailData, function($obMessage) use ($sUserEmail) {
                        $obMessage->to($sUserEmail);
                    });
                    
                } else {
                    Mail::send('lovata.buddies::mail.registration', $arMailData, function($obMessage) use ($sUserEmail) {
                        $obMessage->to($sUserEmail);
                    });
                }
                
                break;
        }

        $obUser->forceSave();
    }
}
