<?php namespace Lovata\Buddies\Components;

use Lovata\Toolbox\Traits\Helpers\TraitComponentNotFoundResponse;
use Lovata\Buddies\Models\User;
use Cms\Classes\ComponentBase;

/**
 * Class ActivationPage
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ActivationPage extends ComponentBase
{
    use TraitComponentNotFoundResponse;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.buddies::lang.component.activation_page',
            'description' => 'lovata.buddies::lang.component.activation_page_desc'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        $arResult = $this->getElementPageProperties();
        return $arResult;
    }

    /**
     * Get element object
     * @return \Illuminate\Http\Response|null
     */
    public function onRun()
    {
        //Get activation code
        $sActivationCode = $this->property('slug');
        if(empty($sActivationCode)) {
            return $this->getErrorResponse();
        }
        
        //Get user by activation code
        $obUser = User::getByActivationCode($sActivationCode)->first();
        if(empty($obUser)) {
            return $this->getErrorResponse();
        }

        $obUser->activation_code = null;
        $obUser->is_activated = true;
        $obUser->activated_at = $obUser->freshTimestamp();
        $obUser->forceSave();
        
        return null;
    }
}
