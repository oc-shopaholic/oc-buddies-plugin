<?php namespace Lovata\Buddies\Components;

use Lovata\Buddies\Facades\AuthHelper;

/**
 * Class Logout
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Logout extends Buddies
{
    /**
     * @return array
     */
    public function componentDetails() {
        return [
            'name'        => 'lovata.buddies::lang.component.logout',
            'description' => 'lovata.buddies::lang.component.logout_desc'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        $arResult = $this->getModeProperty();
        unset($arResult['flash_on']);

        return $arResult;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function onRun()
    {
        if($this->sMode != self::MODE_SUBMIT) {
            return null;
        }

        if(!empty($this->obUser)) {
            AuthHelper::logout();
        }

        return $this->getResponseModeForm();
    }

    /**
     * Logout (ajax)
     */
    public function onAjax()
    {
        if(!empty($this->obUser)) {
            AuthHelper::logout();
        }

        return $this->getResponseModeAjax();
    }
}
