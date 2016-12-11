<?php namespace Lovata\Buddies\Models;

use Illuminate\Database\Query\Builder;
use Kharanenka\Helper\Result;
use Lang;
use Carbon\Carbon;
use October\Rain\Auth\Models\Throttle as ThrottleBase;

/**
 * Class Throttle
 * @package Lovata\Buddies\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 * 
 * @mixin Builder
 * @mixin \Eloquent
 * 
 * @property int $id
 * @property int $user_id
 * @property string $ip_address
 * @property bool $attempts
 * @property Carbon $last_attempt_at
 * @property bool $is_suspended
 * @property Carbon $suspended_at
 * @property bool $is_banned
 * @property Carbon $banned_at
 *
 * @property User $user
 *
 */
class Throttle extends ThrottleBase
{
    protected $table = 'lovata_buddies_throttle';
    public $belongsTo = [
        'user' => ['Lovata\Buddies\Models\User']
    ];

    /**
     * Check user throttle status.
     */
    public function check()
    {
        
        //Check user is banned
        if($this->is_banned) {
            
            $arErrorData = [
                'message'   => Lang::get('lovata.buddies::lang.message.e_user_banned', ['user' => $this->user->getLogin()]),
                'filed'     => null,
            ];
            
            Result::setFalse($arErrorData);
            return;
        }

        //Check user is suspended
        if($this->checkSuspended()) {

            $arErrorData = [
                'message'   => Lang::get('lovata.buddies::lang.message.e_user_suspended', ['user' => $this->user->getLogin()]),
                'filed'     => null,
            ];

            Result::setFalse($arErrorData);
            return;
        }

        Result::setTrue();
    }
}
