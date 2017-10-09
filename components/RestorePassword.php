<?php namespace Lovata\Buddies\Components;

use Mail;
use Lang;
use Input;
use Kharanenka\Helper\Result;
use October\Rain\Support\Collection;
use Lovata\Buddies\Models\Settings;
use Lovata\Buddies\Models\User;

/**
 * Class RestorePassword
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class RestorePassword extends Buddies
{
    /**
     * @return array
     */
    public function componentDetails() {
        return [
            'name'        => 'lovata.buddies::lang.component.restore_password',
            'description' => 'lovata.buddies::lang.component.restore_password_desc'
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
     * Trigger the password reset email
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function onRun()
    {
        if($this->sMode != self::MODE_SUBMIT) {
            return null;
        }

        $arUserData = Input::all();
        if(empty($arUserData)) {
            return null;
        }
        
        $this->sendRestoreMail($arUserData);
        return $this->getResponseModeForm();
    }

    /**
     * Send restore password mail (ajax request)
     * @return \Illuminate\Http\RedirectResponse|array
     */
    public function onSendMail()
    {
        //Get user data
        $arUserData = Input::all();
        $this->sendRestoreMail($arUserData);

        return $this->getResponseModeAjax();
    }

    /**
     * Send restore password mail
     * @param $arUserData
     * @return bool
     */
    public function sendRestoreMail($arUserData)
    {
        if(empty($arUserData) || !is_array($arUserData)) {

            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setMessage($sMessage);
            return false;
        }

        //Check user auth
        if(!empty($this->obUser)) {

            $sMessage = Lang::get('lovata.buddies::lang.message.e_auth_fail');
            Result::setMessage($sMessage);
            return false;
        }

        //Make collection
        $obUserData = Collection::make($arUserData);
        if(empty($obUserData->get('email'))) {

            $sMessage = Lang::get('system::validation.required',
                ['attribute' => Lang::get('lovata.toolbox::lang.field.email')]
            );

            Result::setFalse(['field' => 'email'])->setMessage($sMessage);
            return false;
        }

        //Get User object
        /** @var User $obUser */
        $obUser = User::active()->getByEmail($obUserData->get('email'))->first();
        if(empty($obUser)) {

            $sMessage = Lang::get('lovata.buddies::lang.message.e_user_not_found',
                ['user' => $obUserData->get('email')]
            );

            Result::setFalse(['field' => 'email'])->setMessage($sMessage);
            return false;
        }

        $arData = [
            'name'     => $obUser->name,
            'code'     => $obUser->getRestoreCode(),
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

        $sMessage = Lang::get('lovata.buddies::lang.message.restore_mail_send_success');
        Result::setMessage($sMessage)->setTrue($obUser->id);

        return true;
    }
}
