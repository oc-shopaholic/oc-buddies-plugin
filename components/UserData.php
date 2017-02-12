<?php namespace Lovata\Buddies\Components;

use Input;
use Kharanenka\Helper\Result;
use Lang;
use Lovata\CustomBuddies\Classes\UserExtend;
use Lovata\Toolbox\Classes\ComponentTraitNotFoundResponse;
use Lovata\Buddies\Models\User;
use Redirect;
use Validator;
use October\Rain\Database\Builder;
use System\Classes\PluginManager;

/**
 * Class UserData
 * @package Lovata\Buddies\Components
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin Builder
 * @mixin \Eloquent
 */
class UserData extends Buddies
{
    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'          => 'lovata.buddies::lang.component.user_data',
            'description'   => 'lovata.buddies::lang.component.user_data_desc'
        ];
    }

    /**
     * Get auth user data
     * @return array|null
     */
    public function get()
    {
        if(empty($this->obUser)) {
            return null;
        }

        return $this->obUser->getData();
    }

    /**
     * Get auth user data (ajax)
     * @return array|null
     */
    public function onGet()
    {
        return $this->get();
    }
}
