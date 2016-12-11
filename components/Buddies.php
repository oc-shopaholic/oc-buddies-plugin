<?php namespace Lovata\Buddies\Components;

use Lovata\Buddies\Models\Settings;
use Mail;
use Lang;
use Lovata\Buddies\Facades\BuddiesAuth;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use Validator;
use Input;
use Cms\Classes\CodeBase;
use Cms\Classes\ComponentBase;
use Session;

/**
 * Class Buddies
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Buddies extends ComponentBase {

    /** @var null|User */
    protected $obUser = null;

    public function componentDetails() {
        return [
            'name'        => 'lovata.buddies::lang.component.buddies',
            'description' => 'lovata.buddies::lang.component.buddies_desc'
        ];
    }

    public function __construct(CodeBase $cmsObject = null, $properties = [])
    {
        $this->checkAuthUser();
        parent::__construct($cmsObject, $properties);
    }

    protected function checkAuthUser() {
        if (BuddiesAuth::check()) {
            $this->obUser = BuddiesAuth::getUser();
        }
    }

    /**
     * Logout
     */
    public function onLogout() {

        if(!empty($this->obUser)) {
            BuddiesAuth::logout();
        }
    }

    /**
     * Get auth user data
     * @return array|null
     */
    public function get() {

        if(empty($this->obUser)) {
            return null;
        }

        return $this->obUser->getData();
    }

    /**
     * Get validation error data
     * @param \Illuminate\Validation\Validator $obValidator
     * @return array
     */
    protected function getValidationError(&$obValidator) {
        
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
    protected function getDefaultValidationMessage() {

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
    protected function setValidationMessage($sRule, $sAttribute, $arSettings = []) {

        $arLangSettings = ['attribute' => Lang::get('lovata.toolbox::lang.field.'.$sAttribute)];
        if(!empty($arSettings)) {
            $arLangSettings = array_merge($arLangSettings, $arSettings);
        }

        return Lang::get('lovata.toolbox::lang.validation.'.$sRule, $arLangSettings);
    }

    /**
     * Get old form data
     * @return array|string
     */
    public function getOldFormData() {
        return Input::old('user');
    }

    /**
     * Get error message
     * @return mixed
     */
    public function getErrorMessage() {

        $arResult = [
            'message' => Session::get('message'),
            'field' => Session::get('field'),
        ];

        return $arResult;
    }
}
