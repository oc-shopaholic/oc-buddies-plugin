<?php namespace Lovata\Buddies\Components;

use Input;
use Validator;
use Kharanenka\Helper\Result;
use Lang;
use Lovata\Toolbox\Traits\Helpers\TraitComponentNotFoundResponse;
use Lovata\Buddies\Models\User;
use Redirect;
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

    use TraitComponentNotFoundResponse;

    protected $sMode = null;

    /** @var null|User */
    protected $obElement = null;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name' => 'lovata.buddies::lang.component.user_page',
            'description' => 'lovata.buddies::lang.component.user_page_desc'
        ];
    }

    /**
     * Define plugin properties
     * @return array
     */
    public function defineProperties()
    {
        $arProperties = $this->getElementPageProperties();
        $arProperties = array_merge($arProperties, $this->getModeProperty());
        return $arProperties;
    }

    /**
     * Init component data
     */
    public function init()
    {
        $this->sMode = $this->property('mode');
        if(empty($this->sMode)) {
            $this->sMode = self::MODE_AJAX;
        }
    }

    /**
     * Get element object
     * @return \Illuminate\Http\Response|null
     */
    public function onRun()
    {
        //Get element slug
        $sElementSlug = $this->property('slug');
        if (empty($sElementSlug) || empty($this->obUser) || ($this->obUser->id != $sElementSlug)) {
            return $this->getErrorResponse();
        }

        // Resolve show data or update
        $arUserData = Input::get('user');
        if (empty($arUserData)) {
            return null;
        }

        $this->updateUserData($arUserData);
        return null;
    }

    /**
     * Registration (ajax request)
     * @return \Illuminate\Http\RedirectResponse|array
     */
    public function onAjax()
    {
        //Get user data
        $arUserData = Input::get('user');
        $this->updateUserData($arUserData);

        return $this->getResponseModeAjax();
    }

    /**
     * Update user data
     * @param array $arUserData
     *
     * @return void
     */
    protected function updateUserData($arUserData)
    {
        if(empty($arUserData) || empty($this->obUser)) {
            $arErrorData = [
                'message'   => Lang::get('lovata.toolbox::lang.message.e_not_correct_request'),
                'field'     => null,
            ];

            Result::setFalse($arErrorData);
            return;
        }

        $arMessages = $this->getDefaultValidationMessage();
        $arMessages['email.unique'] = Lang::get('lovata.buddies::lang.message.e_email_unique');

        //Default validation
        $obValidator = Validator::make($arUserData, User::getValidationRules(), $arMessages);

        if($obValidator->fails()) {
            $arErrorData = $this->getValidationError($obValidator);
            Result::setFalse($arErrorData);
            return;
        }
        // Get no-update fields values from DB
        $arUserData = UserExtend::filerUpdateData($this->obUser, $arUserData);

        $this->obUser->update($arUserData);
        $this->obUser->save();

        $arResult = [
            'message'   => Lang::get('lovata.buddies::lang.message.user_update_success'),
            'user'     => $this->obUser->getData(),
        ];

        Result::setTrue($arResult);
    }
}
