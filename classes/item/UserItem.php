<?php namespace Lovata\Buddies\Classes\Item;

use Lovata\Buddies\Models\User;

use Lovata\Toolbox\Classes\Item\ElementItem;

/**
 * Class UserItem
 * @package Lovata\Buddies\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA User
 *
 * @see     \Lovata\Buddies\Tests\Unit\Item\UserItemTest
 *
 * @property                     $id
 * @property string              $email
 * @property string              $name
 * @property string              $last_name
 * @property string              $middle_name
 * @property string              $phone
 * @property array               $phone_list
 * @property array               $socialite_token
 * @property \System\Models\File $avatar
 * @property array               $property
 *
 * Orders for Shopaholic plugin
 * @property \Lovata\OrdersShopaholic\Classes\Collection\OrderCollection $order
 */
class UserItem extends ElementItem
{
    const MODEL_CLASS = User::class;

    /** @var User */
    protected $obElement = null;

    /**
     * Returns true, if user has socialite token with code == $sCode
     * @param string $sCode
     * @return bool
     */
    public function hasSocialToken($sCode)
    {
        if (empty($this->socialite_token) || empty($sCode)) {
            return false;
        }

        return in_array($sCode, $this->socialite_token);
    }

    /**
     * Get additional element data for cache array
     * @return array
     */
    protected function getElementData()
    {
        $arResult = [
            'socialite_token' => (array) $this->obElement->socialite_token()->lists('code'),
        ];

        return $arResult;
    }
}
