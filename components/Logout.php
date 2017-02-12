<?php namespace Lovata\Buddies\Components;

use Lang;
use Cms\Classes\Page;
use Lovata\Buddies\Facades\BuddiesAuth;

/**
 * Class Logout
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Logout extends Buddies
{
    protected $sMode = null;

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
        $arResult = [
            'mode' => [
                'title'             => 'lovata.buddies::lang.component.property_mode',
                'type'              => 'dropdown',
                'options'           => [
                    self::MODE_SUBMIT      => Lang::get('lovata.buddies::lang.component.mode_'.self::MODE_SUBMIT),
                    self::MODE_AJAX        => Lang::get('lovata.buddies::lang.component.mode_'.self::MODE_AJAX),
                ],
            ],
            'redirect_on' => [
                'title'             => 'lovata.buddies::lang.component.property_redirect_on',
                'type'              => 'checkbox',
            ],
        ];

        $arPageList = Page::getNameList();
        if(!empty($arPageList)) {
            $arResult['redirect_page'] = [
                'title'             => 'lovata.buddies::lang.component.property_redirect_page',
                'type'              => 'dropdown',
                'options'           => $arPageList,
            ];
        }

        return $arResult;
    }

    /**
     * Init component data
     */
    protected function initData()
    {
        $this->sMode = $this->property('mode');
        if(empty($this->sMode)) {
            $this->sMode = self::MODE_AJAX;
        }
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
            BuddiesAuth::logout();
        }

        return $this->getResponseModeForm();
    }

    /**
     * Logout (ajax)
     */
    public function onAjax()
    {
        $this->initData();
        if(!empty($this->obUser)) {
            BuddiesAuth::logout();
        }

        return $this->getResponseModeAjax();
    }
}
