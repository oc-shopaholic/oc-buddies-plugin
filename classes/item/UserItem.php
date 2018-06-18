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
}
