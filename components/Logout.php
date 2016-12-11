<?php namespace Lovata\Buddies\Components;

use Lang;
use Redirect;

/**
 * Class Logout
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Logout extends Buddies  {

    public function componentDetails() {
        return [
            'name'        => 'lovata.buddies::lang.component.logout',
            'description' => 'lovata.buddies::lang.component.logout_desc'
        ];
    }

    public function defineProperties() {
        return [
            'redirect_on' => [
                'title'             => Lang::get('lovata.buddies::lang.component.property_redirect_on'),
                'description'             => Lang::get('lovata.buddies::lang.component.property_redirect_on_desc'),
                'type'              => 'checkbox',
            ],
            'redirect_url' => [
                'title'             => Lang::get('lovata.buddies::lang.component.property_redirect_url'),
                'type'              => 'string',
            ],
        ];
    }
    
    public function onRun()
    {
        $this->onLogout();

        $bRedirectOn = $this->property('redirect_on');
        $sRedirectURL = $this->property('redirect_url');

        if(!$bRedirectOn) {
            return;
        }

        if(empty($sRedirectURL)) {
            return Redirect::to('/');
        }

        return Redirect::to($sRedirectURL);
    }
}
