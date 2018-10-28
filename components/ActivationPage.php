<?php namespace Lovata\Buddies\Components;

use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Lovata\Toolbox\Classes\Helper\PageHelper;
use Lovata\Toolbox\Traits\Helpers\TraitComponentNotFoundResponse;

use Lovata\Buddies\Models\User;
use Lovata\Buddies\Facades\AuthHelper;

/**
 * Class ActivationPage
 * @package Lovata\Buddies\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ActivationPage extends ComponentBase
{
    use TraitComponentNotFoundResponse;

    /** @var Buddies */
    protected $obUser;

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
        $arResult = [
            'slug'          => [
                'title'   => 'lovata.toolbox::lang.component.property_slug',
                'type'    => 'string',
                'default' => '{{ :slug }}',
            ],
            'force_login' => [
                'title' => 'lovata.buddies::lang.component.property_force_login',
                'type'  => 'checkbox',
            ],
            'redirect_on' => [
                'title' => 'lovata.toolbox::lang.component.property_redirect_on',
                'type'  => 'checkbox',
            ],
        ];

        try {
            $arPageList = Page::getNameList();
        } catch (\Exception $obException) {
            $arPageList = [];
        }

        if (!empty($arPageList)) {
            $arResult['redirect_page'] = [
                'title'             => 'lovata.toolbox::lang.component.property_redirect_page',
                'type'              => 'dropdown',
                'options'           => $arPageList,
            ];
        }

        return $arResult;
    }

    /**
     * Get element object
     * @return \Illuminate\Http\Response|null|\Illuminate\Http\RedirectResponse
     * @throws \October\Rain\Exception\AjaxException
     */
    public function onRun()
    {
        //Get activation code
        $sActivationCode = $this->property('slug');
        if (empty($sActivationCode)) {
            return $this->getErrorResponse();
        }

        //Get user by activation code
        $this->obUser = User::getByActivationCode($sActivationCode)->first();
        if (empty($this->obUser)) {
            return $this->getErrorResponse();
        }

        $this->obUser->activate();
        $this->obUser->forceSave();

        if ($this->property('force_login')) {
            AuthHelper::login($this->obUser);
        }

        $bRedirectOn = $this->property('redirect_on');
        $sRedirectPage = $this->property('redirect_page');
        if (!$bRedirectOn) {
            return null;
        }

        if (empty($sRedirectPage)) {
            return Redirect::to('/');
        }

        $arPagePropertyList = [];
        $arPropertyList = PageHelper::instance()->getUrlParamList($sRedirectPage, 'UserPage');
        if (!empty($arPropertyList)) {
            $arPagePropertyList[array_shift($arPropertyList)] = $this->obUser->id;
        }

        $sRedirectURL = Page::url($sRedirectPage, $arPagePropertyList);

        return Redirect::to($sRedirectURL);
    }
}
