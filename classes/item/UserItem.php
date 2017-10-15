<?php namespace Lovata\Buddies\Classes\Item;

use Lovata\Shopaholic\Plugin;
use Lovata\Buddies\Models\User;

use Lovata\Toolbox\Classes\Item\ElementItem;

/**
 * Class UserItem
 * @package Lovata\Buddies\Classes\Item
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA User
 *
 * @see \Lovata\Buddies\Tests\Unit\Item\UserItemTest
 *
 * @property        $id
 * @property string $email
 * @property string $name
 * @property string $last_name
 * @property string $middle_name
 * @property string $phone
 * @property array $phone_list
 * @property array $avatar
 * @property array $property
 */
class UserItem extends ElementItem
{
    const CACHE_TAG_ELEMENT = 'buddies-user-element';

    /** @var User */
    protected $obElement = null;

    /**
     * Set element object
     */
    protected function setElementObject()
    {
        if(!empty($this->obElement) && ! $this->obElement instanceof User) {
            $this->obElement = null;
        }

        if(!empty($this->obElement) || empty($this->iElementID)) {
            return;
        }

        $this->obElement = User::find($this->iElementID);
    }

    /**
     * Get cache tag array for model
     * @return array
     */
    protected static function getCacheTag()
    {
        return [Plugin::CACHE_TAG, self::CACHE_TAG_ELEMENT];
    }

    /**
     * Set element data from model object
     *
     * @return array
     */
    protected function getElementData()
    {
        if(empty($this->obElement)) {
            return null;
        }

        $arResult = [
            'id'          => $this->obElement->id,
            'email'       => $this->obElement->email,
            'name'        => $this->obElement->name,
            'last_name'   => $this->obElement->last_name,
            'middle_name' => $this->obElement->middle_name,
            'phone'       => $this->obElement->phone,
            'phone_list'  => $this->obElement->phone_list,
            'avatar'      => $this->obElement->getFileData('avatar'),
            'property'    => $this->obElement->property,
        ];

        return $arResult;
    }
}