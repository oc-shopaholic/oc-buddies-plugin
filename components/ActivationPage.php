<?php namespace Lovata\Buddies\Components;

use Lang;
use Lovata\Buddies\Facades\BuddiesAuth;
use Response;
use Lovata\Buddies\Models\User;
use Cms\Classes\ComponentBase;

/**
 * Class ActivationPage
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ActivationPage extends ComponentBase {

    public function componentDetails() {
        return [
            'name'        => 'lovata.buddies::lang.component.activation_page',
            'description' => 'lovata.buddies::lang.component.activation_page_desc'
        ];
    }

    public function defineProperties() {
        return [
            'code' => [
                'title'             => Lang::get('lovata.buddies::lang.component.property_code'),
                'type'              => 'string',
                'default'           => '{{ :code }}',
            ],
        ];
    }

    /**
     * Get element object
     * @return \Illuminate\Http\Response|void
     */
    public function onRun() {

        //Get activation code
        $sActivationCode = $this->property('code');
        if(empty($sActivationCode)) {
            return Response::make($this->controller->run('404')->getContent(), 404);
        }
        
        //Get user by activation code
        $obUser = User::getByActivationCode($sActivationCode)->first();
        if(empty($obUser)) {
            return Response::make($this->controller->run('404')->getContent(), 404);
        }

        $obUser->activation_code = null;
        $obUser->is_activated = true;
        $obUser->activated_at = $obUser->freshTimestamp();
        $obUser->forceSave();
        
        return;
    }
}
