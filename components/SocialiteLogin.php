<?php namespace Lovata\Buddies\Components;

use Redirect;
use Cms\Classes\Page;
use Lovata\Toolbox\Classes\Helper\PageHelper;

use Lovata\Buddies\Models\User;
use Lovata\Buddies\Facades\AuthHelper;
use Lovata\Buddies\Models\SocialiteToken;

/**
 * Class Login
 * @package Lovata\Buddies\Components
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class SocialiteLogin extends Buddies
{
    const PROPERTY_SOCIALITE_CODE = 'code';
    const PROPERTY_REDIRECT_SUCCESS_PAGE = 'redirect_success_page';
    const PROPERTY_REDIRECT_FAIL_PAGE = 'redirect_cancel_page';

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.buddies::lang.component.socialite_login',
            'description' => 'lovata.buddies::lang.component.socialite_login_desc',
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        $arResult = [
            self::PROPERTY_SOCIALITE_CODE => [
                'title' => 'lovata.buddies::lang.component.property_socialite_code',
                'type'  => 'text',
            ]
        ];

        try {
            $arPageList = Page::getNameList();
        } catch (\Exception $obException) {
            $arPageList = [];
        }

        if (!empty($arPageList)) {
            $arResult[self::PROPERTY_REDIRECT_SUCCESS_PAGE] = [
                'title'   => 'lovata.toolbox::lang.component.property_redirect_success_page',
                'type'    => 'dropdown',
                'options' => $arPageList,
            ];
            $arResult[self::PROPERTY_REDIRECT_FAIL_PAGE] = [
                'title'   => 'lovata.toolbox::lang.component.property_redirect_fail_page',
                'type'    => 'dropdown',
                'options' => $arPageList,
            ];
        }

        return $arResult;
    }

    /**
     * Auth user
     * @return \Illuminate\Http\RedirectResponse|null
     * @throws \Exception
     */
    public function onRun()
    {
        $sDriverCode = $this->getDriverCode();
        if (empty($sDriverCode) || !class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
            return $this->returnFailRedirect();
        }

        if (!empty($this->obUser)) {
            return $this->returnSuccessRedirect();
        }

        return $this->login($sDriverCode);
    }

    /**
     * User login by socialite token
     * @param $sDriverCode
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    protected function login($sDriverCode)
    {
        try {
            /** @var \SocialiteProviders\Manager\OAuth2\User $obUserData */
            $obUserData = \Laravel\Socialite\Facades\Socialite::driver($sDriverCode)->user();
        } catch (\Exception $obException) {
            return $this->returnFailRedirect();
        }

        if (empty($obUserData)) {
            return $this->returnFailRedirect();
        }

        $this->findUserByToken($sDriverCode, $obUserData);
        $this->findUserByEmail($sDriverCode, $obUserData);
        $this->createNewUser($sDriverCode, $obUserData);

        if (!empty($this->obUser)) {
            AuthHelper::login($this->obUser);
            return $this->returnSuccessRedirect();
        }

        return $this->returnFailRedirect();
    }

    /**
     * Find user by driver and socialite token
     * @param string                                  $sDriverCode
     * @param \SocialiteProviders\Manager\OAuth2\User $obUserData
     */
    protected function findUserByToken($sDriverCode, $obUserData)
    {
        if (empty($sDriverCode) || empty($obUserData)) {
            return;
        }

        $sExternalID = $obUserData->getId();
        if (empty($sExternalID)) {
            return;
        }

        $obSocialiteToken = SocialiteToken::getByCode($sDriverCode)->getByExternalID($sExternalID)->first();
        if (empty($obSocialiteToken)) {
            return;
        }

        $this->obUser = $obSocialiteToken->user;
    }

    /**
     * Find user by driver and socialite email
     * @param string                                  $sDriverCode
     * @param \SocialiteProviders\Manager\OAuth2\User $obUserData
     */
    protected function findUserByEmail($sDriverCode, $obUserData)
    {
        if (empty($sDriverCode) || empty($obUserData) || !empty($this->obUser)) {
            return;
        }

        //Get email from user data object
        $sExternalID = $obUserData->getId();
        if (empty($sExternalID)) {
            return;
        }

        $sEmail = $obUserData->getEmail();
        if (empty($sEmail)) {
            $sEmail = 'fake'.$sDriverCode.$sExternalID.'@fake.com';
        }

        //Find user by email
        $this->obUser = User::getByEmail($sEmail)->first();
        if (empty($this->obUser)) {
            return;
        }

        //Create new socialite token
        SocialiteToken::create([
            'code'        => $sDriverCode,
            'user_id'     => $this->obUser->id,
            'external_id' => $sExternalID,
        ]);
    }

    /**
     * Create new user by socialite data
     * @param string                                  $sDriverCode
     * @param \SocialiteProviders\Manager\OAuth2\User $obUserData
     */
    protected function createNewUser($sDriverCode, $obUserData)
    {
        if (empty($sDriverCode) || empty($obUserData) || !empty($this->obUser)) {
            return;
        }

        $sExternalID = $obUserData->getId();
        if (empty($sExternalID)) {
            return;
        }

        $sPassword = $sDriverCode.$sExternalID;
        $sEmail = $obUserData->getEmail();
        if (empty($sEmail)) {
            $sEmail = 'fake'.$sDriverCode.$sExternalID.'@fake.com';
        }

        $arUserData = [
            'email'                 => $sEmail,
            'name'                  => $obUserData->getName(),
            'password'              => $sPassword,
            'password_confirmation' => $sPassword,
        ];

        try {
            //Create new user
            $this->obUser = AuthHelper::register($arUserData, true);
        } catch (\October\Rain\Database\ModelException $obException) {
            return;
        }

        //Create new socialite token
        SocialiteToken::create([
            'code'        => $sDriverCode,
            'user_id'     => $this->obUser->id,
            'external_id' => $sExternalID,
        ]);
    }

    /**
     * @return string
     */
    protected function getDriverCode()
    {
        return $this->property(self::PROPERTY_SOCIALITE_CODE);
    }

    /**
     * Get page name and return success response
     * @return \Illuminate\Http\RedirectResponse|array
     */
    protected function returnSuccessRedirect()
    {
        $sRedirectPage = $this->property(self::PROPERTY_REDIRECT_SUCCESS_PAGE);
        if (empty($sRedirectPage)) {
            return Redirect::to('/');
        }

        $arPageParamList = [];
        $arParamList = (array) PageHelper::instance()->getUrlParamList($sRedirectPage, 'UserPage');
        $sPageParam = array_shift($arParamList);
        if (!empty($sPageParam)) {
            $arPageParamList[$sPageParam] = $this->obUser->id;
        }

        $sRedirectURL = Page::url($sRedirectPage, $arPageParamList);

        return Redirect::to($sRedirectURL);
    }

    /**
     * Get page name and return success response
     * @return \Illuminate\Http\RedirectResponse|array
     */
    protected function returnFailRedirect()
    {
        $sRedirectPage = $this->property(self::PROPERTY_REDIRECT_FAIL_PAGE);
        if (empty($sRedirectPage)) {
            return Redirect::to('/');
        }

        $sRedirectURL = Page::url($sRedirectPage);

        return Redirect::to($sRedirectURL);
    }
}

