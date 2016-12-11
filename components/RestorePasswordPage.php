<?php namespace Lovata\Buddies\Components;

use Lovata\Buddies\Models\Settings;
use Mail;
use Lang;
use Lovata\Buddies\Models\User;
use Validator;
use Input;
use Redirect;

/**
 * Class RestorePasswordPage
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class RestorePasswordPage extends Buddies  {

    public function componentDetails() {
        return [
            'name'        => 'lovata.buddies::lang.component.restore_password_page',
            'description' => 'lovata.buddies::lang.component.restore_password_page_desc'
        ];
    }

    public function defineProperties() {
        return [
            'redirect_url' => [
                'title'             => Lang::get('lovata.buddies::lang.component.property_redirect_url'),
                'type'              => 'string',
            ],
        ];
    }

    /**
     * Trigger the password reset email
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function onRun()
    {
        $arUserData = Input::get('user');
        if(empty($arUserData)) {
            return;
        }
        
        if(!empty($this->obUser)) {
            
            $arErrorData = [
                'message'   => Lang::get('lovata.buddies::lang.message.e_auth_fail'),
                'field'     => null,
            ];

            return Redirect::back()->withInput()->with($arErrorData);
        }

        //Get validation data
        $arMessages = $this->getDefaultValidationMessage();
        $arRules = [
            'email' => 'required|email',
        ];

        $obValidator = Validator::make($arUserData, $arRules, $arMessages);
        if($obValidator->fails()) {
            
            $arErrorData = $this->getValidationError($obValidator);
            return Redirect::back()->withInput()->with($arErrorData);
        }

        //Get User object
        /** @var User $obUser */
        $obUser = User::active()->getByEmail($arUserData['email'])->first();
        if(empty($obUser)) {
            
            $arErrorData = [
                'message'   => Lang::get('lovata.buddies::lang.message.e_user_not_found', ['user' => $arUserData['email']]),
                'field'     => 'email',
            ];

            return Redirect::back()->withInput()->with($arErrorData);
        }

        $arData = [
            'name' => $obUser->name,
            'code' => $obUser->getRestoreCode(),
            'site_url' => env('SITE_URL'),
        ];

        $sUserEmail = $obUser->email;
        
        //Get queue settings
        $bUseQueue = Settings::getValue('queue_on');
        $sQueueName = Settings::getValue('queue_name');

        //Send restore mail
        if($bUseQueue && empty($sQueueName)) {
            Mail::queue('lovata.buddies::mail.restore', $arData, function($message) use ($sUserEmail) {
                $message->to($sUserEmail);
            });
        } else if ($bUseQueue && !empty($sQueueName)) {
            Mail::queueOn($sQueueName, 'lovata.buddies::mail.restore', $arData, function($message) use ($sUserEmail) {
                $message->to($sUserEmail);
            });
        } else {
            Mail::send('lovata.buddies::mail.restore', $arData, function($message) use ($sUserEmail) {
                $message->to($sUserEmail);
            });
        }

        $sRedirectURL = $this->property('redirect_url');
        if(empty($sRedirectURL)) {
            return Redirect::to('/');
        }

        return Redirect::to($sRedirectURL);
    }
}
