<?php namespace Lovata\Buddies\Components;

use Cms\Classes\ComponentBase;
use Lovata\Buddies\Models\User;
use Lovata\Toolbox\Traits\Helpers\TraitComponentNotFoundResponse;

/**
 * Class ActivationPage
 * @package Lovata\Buddies\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
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
            'description' => 'lovata.buddies::lang.component.activation_page_desc',
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        $arResult = $this->getElementPageProperties();
        unset($arResult['slug_required']);

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
        if (empty($sActivationCode)) {
            return $this->getErrorResponse();
        }

        //Get user by activation code
        $obUser = User::getByActivationCode($sActivationCode)->first();
        if (empty($obUser)) {
            return $this->getErrorResponse();
        }

        $obUser->activate();
        $obUser->forceSave();

        return null;
    }
}
