<?php namespace Lovata\Buddies\Components;

use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\Settings;
use Mail;
use Lang;
use Lovata\Buddies\Models\User;
use Validator;
use Input;
use Redirect;

/**
 * Class RestorePassword
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class RestorePassword extends Buddies
{
    protected $sMode = null;

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
     * Trigger the password reset email
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
        
        $this->sendRestoreMail($arUserData);
        return $this->getResponseModeForm();
    }

    /**
     * Send restore password mail (ajax request)
     * @return \Illuminate\Http\RedirectResponse|array
     */
    public function onSendMail()
    {
        $this->initData();

        //Get user data
        $arUserData = Input::get('user');
        $this->sendRestoreMail($arUserData);

        return $this->getResponseModeAjax();
    }

    /**
     * Send restore password mail
     * @param $arUserData
     * @return void
     */
    protected function sendRestoreMail($arUserData)
    {
        if(empty($arUserData)) {
            $arErrorData = [
                'message'   => Lang::get('lovata.buddies::lang.message.e_not_correct_request'),
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

        //Get validation data
        $arMessages = $this->getDefaultValidationMessage();
        $arRules = [
            'email' => 'required|email',
        ];

        $obValidator = Validator::make($arUserData, $arRules, $arMessages);
        if($obValidator->fails()) {

            $arErrorData = $this->getValidationError($obValidator);
            Result::setFalse($arErrorData);
            return;
        }

        //Get User object
        /** @var User $obUser */
        $obUser = User::active()->getByEmail($arUserData['email'])->first();
        if(empty($obUser)) {

            $arErrorData = [
                'message'   => Lang::get('lovata.buddies::lang.message.e_user_not_found', ['user' => $arUserData['email']]),
                'field'     => 'email',
            ];

            Result::setFalse($arErrorData);
            return;
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

        $arResult = [
            'message'   => Lang::get('lovata.buddies::lang.message.restore_mail_send_success'),
            'user'     => $obUser->getData(),
        ];

        Result::setTrue($arResult);
    }
}
