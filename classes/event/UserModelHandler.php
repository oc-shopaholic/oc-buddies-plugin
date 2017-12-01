<?php namespace Lovata\Buddies\Classes\Event;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Buddies\Models\User;
use Lovata\Buddies\Classes\Item\UserItem;

/**
 * Class UserModelHandler
 * @package Lovata\Buddies\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA User
 */
class UserModelHandler extends ModelHandler
{
    /** @var User */
    protected $obElement;

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return User::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return UserItem::class;
    }
}
