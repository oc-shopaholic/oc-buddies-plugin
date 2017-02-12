<?php namespace Lovata\Buddies\Components;

use Input;
use Kharanenka\Helper\Result;
use Lang;
use Lovata\CustomBuddies\Classes\UserExtend;
use Lovata\Toolbox\Classes\ComponentTraitNotFoundResponse;
use Lovata\Buddies\Models\User;
use Redirect;
use Validator;
use October\Rain\Database\Builder;
use System\Classes\PluginManager;

/**
 * Class UserPage
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin Builder
 * @mixin \Eloquent
 */
class UserPage extends Buddies
{

    use ComponentTraitNotFoundResponse;

    /** @var null|User */
    protected $obElement = null;

    public function componentDetails()
    {
        return [
            'name' => 'lovata.buddies::lang.component.user_page',
            'description' => 'lovata.buddies::lang.component.user_page_desc'
        ];
    }

    public function defineProperties()
    {
        $arProperties = $this->getElementPageProperties();
        return $arProperties;
    }

    /**
     * Get element object
     * @return \Illuminate\Http\Response|void
     */
    public function onRun()
    {

        $bDisplayError404 = $this->property('error_404') == 'on' ? true : false;

        //Get element slug
        $sElementSlug = $this->property('slug');
        if (empty($sElementSlug) || empty($this->obUser) || ($this->obUser->id != $sElementSlug)) {
            return $this->getErrorResponse($bDisplayError404);
        }

        // Resolve show data or update
        $arUserData = Input::get('user');
        if (empty($arUserData)) {
            return;
        }

        $this->updateUserData($arUserData);
    }

    public function onUpdateUser()
    {

    }

    public function updateUserData($arUserData)
    {

        if (empty($arUserData)) {
            return Result::setFalse(Lang::get('lovata.buddies::lang.message.e_not_correct_request'))->get();
        }

        // Custom validation
        if (PluginManager::instance()->hasPlugin('Lovata.CustomBuddies')) {
            \Lovata\CustomBuddies\Classes\UserExtend::extendValidation($arUserData);
            if (!Result::flag()) {
                return Redirect::back()->withInput()->with(Result::data());
            }
        }
        // Get no-update fields values from DB
        $arUserData = UserExtend::filerUpdateData($this->obUser, $arUserData);

        $this->obUser->update($arUserData);
        $this->obUser->save();

        return Result::setTrue()->get();
    }
}
