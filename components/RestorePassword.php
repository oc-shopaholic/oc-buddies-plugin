<?php namespace Lovata\Buddies\Components;

use Lang;
use Input;
use Kharanenka\Helper\Result;
use October\Rain\Support\Collection;

use Lovata\Toolbox\Models\Settings;
use Lovata\Toolbox\Classes\Helper\SendMailHelper;

use Lovata\Buddies\Models\User;
use Lovata\Buddies\Classes\Item\UserItem;

/**
 * Class RestorePassword
 * @package Lovata\Buddies\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class RestorePassword extends Buddies
{
    const EMAIL_TEMPLATE_DATA_EVENT = 'lovata.buddies::mail.restore.template.data';

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.buddies::lang.component.restore_password',
            'description' => 'lovata.buddies::lang.component.restore_password_desc',
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
        if ($this->sMode != self::MODE_SUBMIT) {
            return null;
        }

        $arUserData = Input::only(['email']);
        if (empty($arUserData)) {
            return null;
        }

        $this->sendRestoreMail($arUserData);

        return $this->getResponseModeForm();
    }

    /**
     * Send restore password mail (ajax request)
     * @return \Illuminate\Http\RedirectResponse|array
     */
    public function onAjax()
    {
        //Get user data
        $arUserData = Input::only(['email']);
        $this->sendRestoreMail($arUserData);

        return $this->getResponseModeAjax();
    }

    /**
     * Send restore password mail
     * @param array $arUserData
     * @return bool
     */
    public function sendRestoreMail($arUserData)
    {
        if (empty($arUserData) || !is_array($arUserData)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setFalse()->setMessage($sMessage);

            return false;
        }

        //Check user auth
        if (!empty($this->obUser)) {
            $sMessage = Lang::get('lovata.buddies::lang.message.e_auth_fail');
            Result::setFalse()->setMessage($sMessage);

            return false;
        }

        //Make collection
        $obUserData = Collection::make($arUserData);
        if (empty($obUserData->get('email'))) {
            $sMessage = Lang::get(
                'system::validation.required',
                ['attribute' => Lang::get('lovata.toolbox::lang.field.email')]
            );

            Result::setFalse(['field' => 'email'])->setMessage($sMessage);

            return false;
        }

        //Get User object
        /** @var User $obUser */
        $obUser = User::active()->getByEmail($obUserData->get('email'))->first();
        if (empty($obUser)) {
            $sMessage = Lang::get(
                'lovata.buddies::lang.message.e_user_not_found',
                ['user' => $obUserData->get('email')]
            );

            Result::setFalse(['field' => 'email'])->setMessage($sMessage);

            return false;
        }

        //Get mail data
        $arMailData = [
            'user'      => $obUser,
            'user_item' => UserItem::make($obUser->id, $obUser),
            'code'      => $obUser->getRestoreCode(),
            'site_url'  => config('app.url'),
        ];

        $sTemplateName = Settings::getValue('restore_password_mail_template', 'lovata.buddies::mail.restore');

        $obSendMailHelper = SendMailHelper::instance();
        $obSendMailHelper->send(
            $sTemplateName,
            $obUser->email,
            $arMailData,
            self::EMAIL_TEMPLATE_DATA_EVENT,
            true);

        $sMessage = Lang::get('lovata.buddies::lang.message.restore_mail_send_success');
        Result::setMessage($sMessage)->setTrue($obUser->id);

        return true;
    }
}
