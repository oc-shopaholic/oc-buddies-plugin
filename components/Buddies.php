<?php namespace Lovata\Buddies\Components;

use Cms\Classes\Page;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\Settings;
use Lang;
use Lovata\Buddies\Facades\BuddiesAuth;
use Lovata\Buddies\Models\User;
use Input;
use Cms\Classes\CodeBase;
use Cms\Classes\ComponentBase;
use Session;
use Redirect;
use Flash;

/**
 * Class Buddies
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Buddies extends ComponentBase
{
    const MODE_SUBMIT = 'submit';
    const MODE_AJAX = 'ajax';

    /** @var null|User */
    protected $obUser = null;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.buddies::lang.component.buddies',
            'description' => 'lovata.buddies::lang.component.buddies_desc'
        ];
    }

    /**
     * Buddies constructor.
     * @param CodeBase|null $cmsObject
     * @param array $properties
     */
    public function __construct(CodeBase $cmsObject = null, $properties = [])
    {
        if(BuddiesAuth::check()) {
            $this->obUser = BuddiesAuth::getUser();
        }

        parent::__construct($cmsObject, $properties);
    }

    /**
     * Get old form data
     * @return array|string
     */
    public function getOldFormData()
    {
        return Input::old('user');
    }

    /**
     * Get error message
     * @return mixed
     */
    public function getErrorMessage()
    {
        $arResult = [
            'message'   => Session::get('message'),
            'field'     => Session::get('field'),
        ];

        return $arResult;
    }

    /**
     * Get component property "mode"
     * @return array
     */
    protected function getModeProperty()
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
            'flash_on' => [
                'title'             => 'lovata.buddies::lang.component.property_flash_on',
                'type'              => 'checkbox',
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
     * Get response (mode = object)
     * @return array
     */
    protected function getResultModeObject()
    {
        return Result::get();
    }

    /**
     * Get response (mode = form)
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function getResponseModeForm()
    {
        if(!Result::flag()) {
            return Redirect::back()->withInput()->with(Result::data());
        }

        $bRedirectOn = $this->property('redirect_on');
        $sRedirectPage = $this->property('redirect_page');
        if(!$bRedirectOn) {
            return null;
        }

        if(empty($sRedirectPage)) {
            return Redirect::to('/');
        }

        $sRedirectURL = Page::url($sRedirectPage, ['id' => $this->obUser->id]);
        return Redirect::to($sRedirectURL);
    }

    /**
     * Get response (mode = response)
     * @return \Illuminate\Http\RedirectResponse|array
     */
    protected function getResponseModeAjax()
    {
        $bFlashOn = $this->property('flash_on');
        if($bFlashOn) {
            $arResult = Result::data();
            if(isset($arResult['message']) && !empty($arResult['message'])) {
                Flash::error($arResult['message']);
            }
        }

        if(!Result::flag()) {
            return Result::get();
        }

        $bRedirectOn = $this->property('redirect_on');
        $sRedirectPage = $this->property('redirect_page');
        if(!$bRedirectOn) {
            return Result::get();
        }

        if(empty($sRedirectPage)) {
            return Redirect::to('/');
        }

        $sRedirectURL = Page::url($sRedirectPage, ['id' => $this->obUser->id]);
        return Redirect::to($sRedirectURL);
    }

    /**
     * Get validation error data
     * @param \Illuminate\Validation\Validator $obValidator
     * @return array
     */
    protected function getValidationError(&$obValidator)
    {
        $arResult = [
            'message' => null,
            'field' => null,
        ];

        if(empty($obValidator)) {
            return $arResult;
        }

        $obMessages = $obValidator->messages();
        $arFieldList = $obMessages->keys();

        $arResult = [
            'message' => $obMessages->first(),
            'field' => array_shift($arFieldList),
        ];

        return $arResult;
    }

    /**
     * Get default validation messages
     * @return array
     */
    protected function getDefaultValidationMessage()
    {
        //Prepare custom validation messages
        $arResult = [
            'email.required'        => $this->setValidationMessage('required', 'email'),
            'email.email'           => $this->setValidationMessage('email', 'email'),
            'password.required'     => $this->setValidationMessage('required', 'password'),
            'password.max'          => $this->setValidationMessage('max.string', 'password', ['max' => 255]),
            'password.confirmed'    => $this->setValidationMessage('confirmed', 'password'),
        ];

        $iPasswordLengthMin = Settings::getValue('password_limit_min');
        if($iPasswordLengthMin > 0) {
            $arResult['password.min'] = $this->setValidationMessage('min.string', 'password', ['min' => $iPasswordLengthMin]);
        }

        $sPasswordRegexp = Settings::getValue('password_regexp');
        if(!empty($sPasswordRegexp)) {
            $arResult['password.regex'] = $this->setValidationMessage('regex', 'password');
        }

        return $arResult;
    }

    /**
     * Set custom validation message
     * @param string $sRule
     * @param string $sAttribute
     * @param array $arSettings
     * @return string
     */
    protected function setValidationMessage($sRule, $sAttribute, $arSettings = [])
    {
        $arLangSettings = ['attribute' => Lang::get('lovata.toolbox::lang.field.'.$sAttribute)];
        if(!empty($arSettings)) {
            $arLangSettings = array_merge($arLangSettings, $arSettings);
        }

        return Lang::get('lovata.toolbox::lang.validation.'.$sRule, $arLangSettings);
    }
}
