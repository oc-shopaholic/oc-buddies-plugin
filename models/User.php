<?php namespace Lovata\Buddies\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Kharanenka\Helper\CCache;
use Kharanenka\Helper\CustomValidationMessage;
use Kharanenka\Helper\DataFileModel;
use Lovata\Buddies\Plugin;
use October\Rain\Auth\Models\User as UserModel;
use October\Rain\Database\Builder;
use System\Classes\PluginManager;

/**
 * Class User
 * @package Lovata\Buddies\Models
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 * 
 * @mixin Builder
 * @mixin \Eloquent
 * @mixin \Lovata\CustomBuddies\Classes\UserExtend
 * 
 * @property int $id
 * @property bool $is_activated
 * @property string $email
 * @property string $password
 * @property bool $password_change
 * @property string $password_confirmation
 * @property string $name
 * @property string $last_name
 * @property string $activation_code
 * @property string $persist_code
 * @property string $reset_password_code
 * @property string $permissions
 * @property Carbon $activated_at
 * @property Carbon $last_login
 * @property bool $is_superuser
 * @property array $property
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * @property Collection|Group[] $groups
 * 
 * @method static $this active()
 * @method static $this notActive()
 * @method static $this getByActivationCode(string $sActivationCode)
 * @method static $this getByEmail(string $sEmail)
 */
class User extends UserModel
{
    use CustomValidationMessage;
    use DataFileModel;
    
    const CACHE_TAG_ELEMENT = 'buddies-user-element';
    
    public $table = 'lovata_buddies_users';

    public $rules = [];
    public $customMessages = [];
    public $attributeNames = [];
    public $dates = ['created_at', 'updated_at', 'deleted_at', 'activated_at', 'last_login'];
    public $attachOne = ['avatar' => ['System\Models\File']];
    public $casts = ['property' => 'array'];
    public $purgeable = ['password_confirmation', 'password_change'];
    public $appends = [];

    public $belongsToMany = [
        'groups' => ['Lovata\Buddies\Models\Group', 'table' => 'lovata_buddies_users_groups', 'key' => 'user_id']
    ];

    /**
     * User constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->rules = self::getValidationRules();
        
        $this->setCustomMessage(Plugin::NAME, ['required', 'unique', 'max', 'min', 'confirmed', 'email', 'regex']);
        $this->setCustomAttributeName(Plugin::NAME, ['email', 'password']);

        //Custom extending
        if(PluginManager::instance()->hasPlugin('Lovata.CustomBuddies')) {
            \Lovata\CustomBuddies\Classes\UserExtend::extendConstructor($this);
        }
        
        parent::__construct($attributes);
    }

    /**
     * Get validation array rules
     * @return array
     */
    public static function getValidationRules() {
        
        $arResult = [
            'email' => 'required|email|unique:lovata_buddies_users|max:255',
            'password' => 'required:create|max:255|confirmed',
            'password_confirmation' => 'required_with:password|max:255',
        ];

        $iPasswordLengthMin = Settings::getValue('password_limit_min');
        if($iPasswordLengthMin > 0) {
            $arResult['password'] = $arResult['password'].'|min:'.$iPasswordLengthMin;
        }

        $sPasswordRegexp = Settings::getValue('password_regexp');
        if(!empty($sPasswordRegexp)) {
            $arResult['password'] = $arResult['password'].'|regex:%^'.$sPasswordRegexp.'$%';
        }
        
        return $arResult;
    }

    /**
     * @param $sValue
     */
    public function setPasswordAttribute($sValue)
    {
        if(!isset($this->attributes['password']) || empty($this->attributes['password']) || (!empty($sValue) && $this->password_change)) {
            $this->attributes['password'] = $sValue;
        }
    }

    public function afterSave()
    {
        $this->clearCache();
    }

    public function afterDelete()
    {
        $this->clearCache();
    }

    /**
     * Clear cache data
     */
    public function clearCache()
    {
        CCache::clear([Plugin::CACHE_TAG, self::CACHE_TAG_ELEMENT], $this->id);
    }

    /**
     * Get element data
     * @return array
     */
    public function getData()
    {
        $arResult = [
            'id'            => $this->id,
            'email'         => $this->email,
            'name'          => $this->name,
            'last_name'     => $this->last_name,
            'avatar'        => $this->getFileData('avatar'),
            'property'      => $this->getPropertyValue(),
        ];

        //Custom extending
        if(PluginManager::instance()->hasPlugin('Lovata.CustomBuddies')) {
            \Lovata\CustomBuddies\Classes\UserExtend::extendGetData($arResult, $this);
        }

        return $arResult;
    }

    /**
     * Get cached data
     * @param $iElementID
     * @param null|User $obElement
     * @return array|null
     */
    public static function getCacheData($iElementID, $obElement = null)
    {
        if(empty($iElementID)) {
            return null;
        }

        //Get cache data
        $arCacheTags = [Plugin::CACHE_TAG, self::CACHE_TAG_ELEMENT];
        $sCacheKey = $iElementID;

        $arResult = CCache::get($arCacheTags, $sCacheKey);
        if(empty($arResult)) {
            
            //Get element object
            if(empty($obElement)) {
                $obElement = self::active()->find($iElementID);
            }

            if(empty($obElement)) {
                return null;
            }

            $arResult = $obElement->getData();
            
            //Set cache data
            CCache::forever($arCacheTags, $sCacheKey, $arResult);
        }

        //Custom extending
        if(PluginManager::instance()->hasPlugin('Lovata.CustomBuddies')) {
            \Lovata\CustomBuddies\Classes\UserExtend::extendGetCacheData($arResult, $iElementID, $obElement);
        }

        return $arResult;
    }

    /**
     * Get property values
     * @return array
     */
    protected function getPropertyValue()
    {
        $arPropertyList = Property::getPropertyList();
        if(empty($arPropertyList)) {
            return [];
        }

        $arPropertyValues = $this->property;

        $arResult = [];
        foreach($arPropertyList as $arPropertyData) {

            if(empty($arPropertyList)) {
                continue;
            }

            $sValue = null;
            if(!empty($arPropertyValues) && isset($arPropertyValues[$arPropertyData['code']])) {
                $sValue = $arPropertyValues[$arPropertyData['code']];
            }

            $arResult[$arPropertyData['code']] = $arPropertyData;
            $arResult[$arPropertyData['code']]['value'] = $sValue;
        }

        return $arResult;
    }

    /**
     * Get restore code
     * @return string
     */
    public function getRestoreCode()
    {
        return implode('!', [$this->id, $this->getResetPasswordCode()]);
    }

    /**
     * Get restore code value
     * @return string
     */
    public function getRestoreCodeValue()
    {
        return implode('!', [$this->id, $this->reset_password_code]);
    }

    /**
     * Get active elements
     * @param Builder $obQuery
     * @return Builder;
     */
    public function scopeActive($obQuery)
    {
        return $obQuery->where('is_activated', true);
    }

    /**
     * Get not active elements
     * @param Builder $obQuery
     * @return Builder;
     */
    public function scopeNotActive($obQuery) {
        return $obQuery->where('is_activated', false);
    }
    
    /**
     * Get elements by activation code
     * @param Builder $obQuery
     * @param string $sData
     * @return Builder;
     */
    public function scopeGetByActivationCode($obQuery, $sData)
    {
        if(!empty($sData)) {
            $obQuery->where('activation_code', $sData);
        }
        
        return $obQuery;
    }
    
    /**
     * Get elements by email
     * @param Builder $obQuery
     * @param string $sData
     * @return Builder;
     */
    public function scopeGetByEmail($obQuery, $sData)
    {
        if(!empty($sData)) {
            $obQuery->where('email', $sData);
        }
        
        return $obQuery;
    }

    /**
     * Gets a code for when the user is persisted to a cookie or session which identifies the user.
     * @return string
     */
    public function getPersistCode()
    {
        if(empty($this->persist_code)) {
            return parent::getPersistCode();
        }

        return $this->persist_code;
    }
}