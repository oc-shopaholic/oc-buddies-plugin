<?php namespace Lovata\Buddies\Models;

use Event;
use October\Rain\Database\Traits\SoftDelete;

use Kharanenka\Scope\NameField;
use Kharanenka\Helper\DataFileModel;
use October\Rain\Auth\Models\User as UserModel;

use Lovata\Toolbox\Traits\Helpers\TraitCached;
use Lovata\Toolbox\Traits\Models\SetPropertyAttributeTrait;

/**
 * Class User
 * @package Lovata\Buddies\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int                                                                                                                       $id
 * @property bool                                                                                                                      $is_activated
 * @property string                                                                                                                    $email
 * @property string                                                                                                                    $password
 * @property bool                                                                                                                      $password_change
 * @property string                                                                                                                    $password_confirmation
 * @property string                                                                                                                    $name
 * @property string                                                                                                                    $last_name
 * @property string                                                                                                                    $middle_name
 * @property string                                                                                                                    $phone
 * @property string                                                                                                                    $phone_short
 * @property array                                                                                                                     $phone_list
 * @property string                                                                                                                    $activation_code
 * @property string                                                                                                                    $persist_code
 * @property string                                                                                                                    $reset_password_code
 * @property string                                                                                                                    $permissions
 * @property \October\Rain\Argon\Argon                                                                                                 $activated_at
 * @property \October\Rain\Argon\Argon                                                                                                 $last_login
 * @property bool                                                                                                                      $is_superuser
 * @property array                                                                                                                     $property
 * @property \October\Rain\Argon\Argon                                                                                                 $created_at
 * @property \October\Rain\Argon\Argon                                                                                                 $updated_at
 * @property \October\Rain\Argon\Argon                                                                                                 $deleted_at
 *
 * @property \System\Models\File                                                                                                       $avatar
 *
 * @property  \October\Rain\Database\Collection|Group[]                                                                                $groups
 *
 * @property  \October\Rain\Database\Collection|SocialiteToken[]                                                                       $socialite_token
 * @method static \October\Rain\Database\Relations\HasMany|SocialiteToken socialite_token()
 *
 * @method static $this active()
 * @method static $this notActive()
 * @method static $this getByActivationCode(string $sActivationCode)
 * @method static $this getByEmail(string $sEmail)
 *
 * Orders for Shopaholic plugin
 * @property \Lovata\OrdersShopaholic\Models\Order[]|\October\Rain\Database\Collection                                                 $order
 * @method static \October\Rain\Database\Relations\HasMany|\Lovata\OrdersShopaholic\Models\Order order()
 * @property \Lovata\OrdersShopaholic\Classes\Collection\OrderCollection|\Lovata\OrdersShopaholic\Classes\Item\OrderItem[]             $order_list
 * @property \Lovata\OrdersShopaholic\Models\UserAddress[]|\October\Rain\Database\Collection                                           $address
 * @method static \October\Rain\Database\Relations\HasMany|\Lovata\OrdersShopaholic\Models\UserAddress address()
 * @property \Lovata\OrdersShopaholic\Classes\Collection\UserAddressCollection|\Lovata\OrdersShopaholic\Classes\Item\UserAddressItem[] $address_list
 */
class User extends UserModel
{
    use SoftDelete;
    use DataFileModel;
    use NameField;
    use TraitCached;
    use SetPropertyAttributeTrait;

    const EVENT_BEFORE_LOGIN = 'lovata.buddies.before.login';
    const EVENT_AFTER_LOGIN = 'lovata.buddies.after.login';
    const EVENT_LOGOUT = 'lovata.buddies.logout';

    public $table = 'lovata_buddies_users';

    public $rules = [
        'email'                 => 'required|email|unique:lovata_buddies_users|max:255',
        'password'              => 'required:create|max:255|confirmed',
        'password_confirmation' => 'required_with:password|max:255',
        'avatar'                => 'image|nullable',
    ];
    public $attributeNames = [
        'email'    => 'lovata.toolbox::lang.field.email',
        'password' => 'lovata.toolbox::lang.field.password',
    ];

    public $fillable = [
        'email',
        'password',
        'password_change',
        'password_confirmation',
        'name',
        'last_name',
        'middle_name',
        'middle_name',
        'phone',
        'phone_list',
        'avatar',
        'property',
    ];

    public $cached = [
        'id',
        'email',
        'name',
        'last_name',
        'middle_name',
        'phone',
        'phone_list',
        'avatar',
        'property',
    ];

    public $dates = ['created_at', 'updated_at', 'deleted_at', 'activated_at', 'last_login'];
    public $attachOne = ['avatar' => ['System\Models\File']];
    public $jsonable = ['property'];
    public $purgeable = ['password_confirmation', 'password_change'];
    protected $hashable = ['password', 'persist_code', 'password_confirmation'];
    public $appends = ['phone_list'];

    public $belongsToMany = [
        'groups' => [Group::class, 'table' => 'lovata_buddies_users_groups', 'key' => 'user_id'],
    ];

    public $hasMany = [
        'socialite_token' => [SocialiteToken::class],
    ];

    /**
     * Fire before login event
     */
    public function beforeLogin()
    {
        Event::fire(self::EVENT_BEFORE_LOGIN, [$this]);
    }

    /**
     * Fire after login event
     */
    public function afterLogin()
    {
        parent::afterLogin();

        Event::fire(self::EVENT_AFTER_LOGIN, [$this]);
    }

    /**
     * Before delete model method
     */
    public function beforeDelete()
    {
        $sTime = str_replace('.', '', microtime(true));
        $this->email = 'removed'.$sTime.'@removed.del';
        $this->save();

        $this->socialite_token()->delete();
    }

    /**
     * User activate
     */
    public function activate()
    {
        $this->activation_code = null;
        $this->is_activated = true;
        $this->activated_at = $this->freshTimestamp();
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
     * @param User $obQuery
     * @return User;
     */
    public function scopeActive($obQuery)
    {
        return $obQuery->where('is_activated', true);
    }

    /**
     * Get not active elements
     * @param User $obQuery
     * @return User;
     */
    public function scopeNotActive($obQuery)
    {
        return $obQuery->where('is_activated', false);
    }

    /**
     * Get elements by activation code
     * @param User   $obQuery
     * @param string $sData
     * @return User;
     */
    public function scopeGetByActivationCode($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('activation_code', $sData);
        }

        return $obQuery;
    }

    /**
     * Get elements by email
     * @param User   $obQuery
     * @param string $sData
     * @return User;
     */
    public function scopeGetByEmail($obQuery, $sData)
    {
        if (!empty($sData)) {
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
        if (empty($this->persist_code)) {
            return parent::getPersistCode();
        }

        return $this->persist_code;
    }

    /**
     * Get phone list from "phone" field (',' is delimiter)
     * @return array
     */
    public function getPhoneListAttribute()
    {
        $sPhone = $this->phone;
        if (empty($sPhone)) {
            return [];
        }

        $arResult = [];

        //Explode 'phone' field
        $arPhoneList = explode(',', $sPhone);
        foreach ($arPhoneList as $sPhoneNumber) {
            //Trim phone
            $sPhoneNumber = trim($sPhoneNumber);
            if (empty($sPhoneNumber)) {
                continue;
            }

            //Add phone to result
            $arResult[] = $sPhoneNumber;
        }

        return $arResult;
    }

    /**
     * Set phone list array to "phone" field (',' is delimiter)
     * @param array $arValue
     */
    public function setPhoneListAttribute($arValue)
    {
        if (empty($arValue) || !is_array($arValue)) {
            return;
        }

        //Prepare phone list
        $arPhoneList = [];
        foreach ($arValue as $sValue) {
            $sValue = trim($sValue);
            if (empty($sValue)) {
                continue;
            }

            $arPhoneList[] = $sValue;
        }

        //Save phone list to "phone" field
        $this->phone = implode(',', $arPhoneList);
    }

    /**
     * Set "phone" field + "phone_short"
     * @param string $sValue
     */
    public function setPhoneAttribute($sValue)
    {
        $this->attributes['phone'] = $sValue;
        $this->phone_short = preg_replace("%[^\d,+]%", '', $sValue);
    }

    /**
     * Set password attribute method
     * @param string $sValue
     */
    public function setPasswordAttribute($sValue)
    {
        if ($this->exists && empty($sValue)) {
            unset($this->attributes['password']);
        } else {
            $this->attributes['password'] = $sValue;
        }
    }
}
