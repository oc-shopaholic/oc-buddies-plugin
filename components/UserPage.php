<?php namespace Lovata\Buddies\Components;

use Lang;
use Input;
use Redirect;
use Cms\Classes\Page;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use Lovata\Toolbox\Traits\Helpers\TraitComponentNotFoundResponse;

/**
 * Class UserPage
 * @package Lovata\Buddies\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class UserPage extends Buddies
{
    use TraitComponentNotFoundResponse;

    const LOGIN_PAGE = 'login_page';

    /** @var null|User */
    protected $obElement = null;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.buddies::lang.component.user_page',
            'description' => 'lovata.buddies::lang.component.user_page_desc',
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

        try {
            $arPageList = Page::getNameList();
        } catch (\Exception $obException) {
            $arPageList = [];
        }

        if (!empty($arPageList)) {
            $arProperties[self::LOGIN_PAGE] = [
                'title'             => 'lovata.buddies::lang.component.property_login_page',
                'type'              => 'dropdown',
                'options'           => $arPageList,
            ];
        }

        return $arProperties;
    }

    /**
     * Get element object
     * @throws \October\Rain\Exception\AjaxException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|null
     */
    public function onRun()
    {
        if (empty($this->obUser)) {
            return $this->redirectToLoginPage();
        }

        //Get element slug
        $iUserID = $this->property('slug');
        $bSlugRequired = $this->property('slug_required');

        $bErrorResponse = empty($this->obUser) || ($bSlugRequired && (empty($iUserID) || $this->obUser->id != $iUserID));
        if ($bErrorResponse) {
            return $this->getErrorResponse();
        }

        if ($this->sMode != self::MODE_SUBMIT) {
            return null;
        }

        // Resolve show data or update
        $arUserData = Input::all();
        if (empty($arUserData)) {
            return null;
        }

        $this->updateUserData($arUserData);

        return $this->getResponseModeForm();
    }

    /**
     * Registration (ajax request)
     * @return \Illuminate\Http\RedirectResponse|array
     */
    public function onAjax()
    {
        $iUserID = $this->property('slug');
        $bSlugRequired = $this->property('slug_required');

        $bErrorResponse = empty($this->obUser) || ($bSlugRequired && (empty($iUserID) || $this->obUser->id != $iUserID));
        if ($bErrorResponse) {

            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setFalse()->setMessage($sMessage);

            return $this->getResponseModeAjax();
        }

        if (empty($this->obUser)) {
            return $this->redirectToLoginPage();
        }

        //Get user data
        $arUserData = Input::all();
        $this->updateUserData($arUserData);

        return $this->getResponseModeAjax();
    }

    /**
     * Update user data
     * @param array $arUserData
     *
     * @return bool
     */
    public function updateUserData($arUserData)
    {
        if (empty($arUserData) || empty($this->obUser)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setFalse()->setMessage($sMessage);

            return false;
        }

        try {
            $this->obUser->password = null;
            $this->obUser->fill($arUserData);
            $this->obUser->save();
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);

            return false;
        }

        $sMessage = Lang::get('lovata.buddies::lang.message.user_update_success');
        Result::setMessage($sMessage)->setTrue($this->obUser->id);

        return true;
    }

    /**
     * Redirect to login page, if user not authorized
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToLoginPage()
    {
        $sRedirectPage = $this->property(self::LOGIN_PAGE);
        if (empty($sRedirectPage)) {
            return Redirect::to('/');
        }

        $sRedirectURL = Page::url($sRedirectPage);

        return Redirect::to($sRedirectURL);
    }
}
