<?php namespace Lovata\Buddies\Models;

use Kharanenka\Scope\CodeField;
use Kharanenka\Scope\NameField;
use October\Rain\Auth\Models\Group as GroupBase;
use October\Rain\Database\Traits\Validation;

/**
 * Class Group
 * @package Lovata\Buddies\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $description
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 *
 * @property \October\Rain\Database\Collection|User[] $users
 */
class Group extends GroupBase
{
    use Validation;
    use NameField;
    use CodeField;

    public $table = 'lovata_buddies_groups';

    /**
     * Validation rules
     */
    public $rules = [
        'name' => 'required|between:3,64',
        'code' => 'required|regex:/^[a-zA-Z0-9_\-]+$/|unique:user_groups',
    ];

    public $attributeNames = [
        'name' => 'lovata.toolbox::lang.field.name',
        'code' => 'lovata.toolbox::lang.field.code',
    ];

    public $belongsToMany = [
        'users' => [User::class, 'table' => 'lovata_buddies_users_groups'],
    ];


    public $dates = ['created_at', 'updated_at'];
    public $fillable = [
        'name',
        'code',
        'description',
    ];
}
