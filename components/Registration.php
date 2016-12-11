<?php namespace Lovata\Buddies\Components;

use Lovata\Buddies\Models\Settings;
use Lang;
use Lovata\Buddies\Facades\BuddiesAuth;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use System\Classes\PluginManager;
use Validator;
use Input;
use Redirect;
use Mail;

/**
 * Class Registration
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Registration extends Buddies {

    const ACTIVATION_ON = 'activation_on';
    const ACTIVATION_OFF = 'activation_off';
    const ACTIVATION_MAIL = 'activation_mail';
    
    public function componentDetails() {
        return [
            'name'        => 'lovata.buddies::lang.component.registration',
            'description' => 'lovata.buddies::lang.component.registration_desc'
        ];
    }

    public function defineProperties() {
        return [
            'force_login' => [
                'title'             => Lang::get('lovata.buddies::lang.component.property_force_login'),
                'type'              => 'checkbox',
            ],
            'redirect_on' => [
                'title'             => Lang::get('lovata.buddies::lang.component.property_redirect_on'),
                'description'             => Lang::get('lovata.buddies::lang.component.property_redirect_on_desc'),
                'type'              => 'checkbox',
            ],
            'redirect_url' => [
                'title'             => Lang::get('lovata.buddies::lang.component.property_redirect_url'),
                'type'              => 'string',
            ],
            'activation' => [
                'title'             => Lang::get('lovata.buddies::lang.component.property_activation'),
                'type'              => 'dropdown',
                'options'           => [
                    self::ACTIVATION_OFF => Lang::get('lovata.buddies::lang.component.property_'.self::ACTIVATION_OFF),
                    self::ACTIVATION_ON => Lang::get('lovata.buddies::lang.component.property_'.self::ACTIVATION_ON),
                    self::ACTIVATION_MAIL => Lang::get('lovata.buddies::lang.component.property_'.self::ACTIVATION_MAIL),
                ],
            ],
        ];
    }
    
    public function onRun()
    {
        return Redirect::to('/');
    }

    /**
     * Registration
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function onRegistration()
    {

        $arUserData = Input::get('user');
        if(empty($arUserData)) {

            $arErrorData = [
                'message'   => Lang::get('lovata.buddies::lang.message.e_not_correct_request'),
                'field'     => null,
            ];
            
            Result::setFalse($arErrorData);
        } else {
            $this->registration($arUserData);
        }
        
        $bRedirectOn = $this->property('redirect_on');
        $sRedirectURL = $this->property('redirect_url');
        
        if(!$bRedirectOn) {
            return Result::get();
        }
        
        if(!Result::flag()) {
            return Redirect::back()->withInput()->with(Result::data());
        }
        
        if(empty($sRedirectURL)) {
            return Redirect::to('/');
        }

        return Redirect::to($sRedirectURL);
    }

    /**
     * User registration
     * @param $arUserData
     * @return void
     */
    protected function registration($arUserData) {

        if(empty($arUserData)) {
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
        
        $this->afterRegistrationActivate($obUser);
        
        
        if($this->property('force_login')) {
            BuddiesAuth::login($obUser);
        }

        $this->obUser = $obUser;
        Result::setTrue($obUser->id);
    }

    /**
     * Activate user after registration
     * @param User $obUser
     */
    protected function afterRegistrationActivate(&$obUser) {
        
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
