<?php namespace Lovata\Buddies\Models;

use Model;
use October\Rain\Database\Traits\Validation;

use Kharanenka\Scope\CodeField;
use Kharanenka\Scope\ExternalIDField;
use Kharanenka\Scope\UserBelongsTo;

/**
 * Class SocialiteToken
 * @package Lovata\Buddies\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int                       $id
 * @property int                       $user_id
 * @property string                    $code
 * @property string                    $external_id
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 *
 * @property User                      $user
 * @method static User|\October\Rain\Database\Relations\BelongsTo user()
 */
class SocialiteToken extends Model
{
    use Validation;
    use UserBelongsTo;
    use CodeField;
    use ExternalIDField;

    public $table = 'lovata_buddies_socialite_tokens';

    public $rules = [
        'user_id'     => 'required',
        'code'        => 'required',
        'external_id' => 'required',
    ];

    public $fillable = [
        'user_id',
        'code',
        'external_id',
    ];

    public $dates = ['created_at', 'updated_at'];

    public $belongsTo = [
        'user' => [User::class],
    ];
}
