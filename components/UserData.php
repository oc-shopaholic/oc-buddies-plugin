<?php namespace Lovata\Buddies\Components;

use Lovata\Buddies\Classes\Item\UserItem;

/**
 * Class UserData
 * @package Lovata\Buddies\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class UserData extends Buddies
{
    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.buddies::lang.component.user_data',
            'description' => 'lovata.buddies::lang.component.user_data_desc',
        ];
    }

    /**
     * Get auth user data
     * @return UserItem
     */
    public function get()
    {
        if (empty($this->obUser)) {
            return null;
        }

        return UserItem::make($this->obUser->id);
    }
}
