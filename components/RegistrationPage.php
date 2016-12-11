<?php namespace Lovata\Buddies\Components;

use Kharanenka\Helper\Result;
use Input;
use Redirect;

/**
 * Class RegistrationPage
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class RegistrationPage extends Registration {

    public function componentDetails() {
        return [
            'name'        => 'lovata.buddies::lang.component.registration_page',
            'description' => 'lovata.buddies::lang.component.registration_page_desc'
        ];
    }

    /**
     * Registration
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function onRun()
    {
        $arUserData = Input::get('user');
        if(empty($arUserData)) {
            return;
        } else {
            $this->registration($arUserData);
        }
        
        $bRedirectOn = $this->property('redirect_on');
        $sRedirectURL = $this->property('redirect_url');

        if(!Result::flag()) {
            return Redirect::back()->withInput()->with(Result::data());
        }

        if(!$bRedirectOn) {
            return;
        }
        
        if(empty($sRedirectURL)) {
            return Redirect::to('/');
        }

        return Redirect::to($sRedirectURL);
    }
}
