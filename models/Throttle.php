<?php namespace Lovata\Buddies\Models;

use Lang;
use Kharanenka\Scope\UserBelongsTo;
use Kharanenka\Helper\Result;
use October\Rain\Auth\Models\Throttle as ThrottleBase;

/**
 * Class Throttle
 * @package Lovata\Buddies\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int                       $id
 * @property int                       $user_id
 * @property string                    $ip_address
 * @property bool                      $attempts
 * @property \October\Rain\Argon\Argon $last_attempt_at
 * @property bool                      $is_suspended
 * @property \October\Rain\Argon\Argon $suspended_at
 * @property bool                      $is_banned
 * @property \October\Rain\Argon\Argon $banned_at
 *
 * @property User                      $user
 *
 */
class Throttle extends ThrottleBase
{
    use UserBelongsTo;

    public $table = 'lovata_buddies_throttle';
    public $belongsTo = [
        'user' => [User::class]
    ];

    /**
     * Check user throttle status.
     *
     * @return bool
     */
    public function check()
    {
        //Get user object
        $obUser = $this->user;
        if (empty($obUser)) {
            return true;
        }

        //Check user is banned
        if ($this->is_banned) {

            $sMessage = Lang::get('lovata.buddies::lang.message.e_user_banned', ['user' => $this->user->getLogin()]);
            Result::setFalse()->setMessage($sMessage);

            return false;
        }

        //Check user is suspended
        if ($this->checkSuspended()) {

            $sMessage = Lang::get('lovata.buddies::lang.message.e_user_suspended', ['user' => $this->user->getLogin()]);
            Result::setFalse()->setMessage($sMessage);

            return false;
        }

        return true;
    }
}

